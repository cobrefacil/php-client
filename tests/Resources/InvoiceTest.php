<?php

namespace CobreFacil\Resources;

use CobreFacil\BaseTest;
use CobreFacil\Exceptions\InvalidParamsException;
use CobreFacil\Exceptions\ResourceNotFoundException;
use DateInterval;
use DateTime;

class InvoiceTest extends BaseTest
{
    public function testCreateInvoicePayableWithBankSlip()
    {
        $params = [
            'payable_with' => Invoice::PAYMENT_METHOD_BANKSLIP,
            'customer_id' => $this->getLastCustomerId(),
            'due_date' => date('Y-m-d'),
            'items' => [
                [
                    'description' => 'Teclado',
                    'quantity' => 1,
                    'price' => 4999,
                ],
                [
                    'description' => 'Mouse',
                    'quantity' => 1,
                    'price' => 3999,
                ],
            ],
            'settings' => [
                'late_fee' => [
                    'mode' => 'percentage',
                    'amount' => 10,
                ],
                'interest' => [
                    'mode' => 'daily_percentage',
                    'amount' => 0.1,
                ],
                'discount' => [
                    'mode' => 'fixed',
                    'amount' => 9.99,
                    'limit_date' => 5,
                ],
                'warning' => [
                    'description' => '- Em caso de dúvidas entre em contato com nossa Central de Atendimento.',
                ],
            ],
        ];
        $response = $this->cobrefacil->invoice->create($params);
        $this->assertNotNull($response['id']);
        $this->assertEquals(Invoice::STATUS_PROCESSING, $response['status']);
    }

    public function testCreateInvoicePayableWithCredit()
    {
        $params = [
            'payable_with' => Invoice::PAYMENT_METHOD_CREDIT,
            'customer_id' => $this->getLastCustomerId(),
            'credit_card_id' => $this->getLastCardId(),
            'capture' => 1,
            'items' => [
                [
                    'description' => 'Teclado',
                    'quantity' => 1,
                    'price' => 4999,
                ],
                [
                    'description' => 'Mouse',
                    'quantity' => 1,
                    'price' => 3999,
                ],
            ],
        ];
        $response = $this->cobrefacil->invoice->create($params);
        $this->assertNotNull($response['id']);
        $this->assertEquals(Invoice::PAYMENT_METHOD_CREDIT, $response['payable_with']);
        $this->assertEquals(Invoice::STATUS_PROCESSING, $response['status']);
    }

    public function testCreateInvoicePayableWithPix()
    {
        $params = [
            'payable_with' => Invoice::PAYMENT_METHOD_PIX,
            'customer_id' => $this->getLastCustomerId(),
            'due_date' => date('Y-m-d'),
            'items' => [
                [
                    'description' => 'Teclado',
                    'quantity' => 1,
                    'price' => 4999,
                ],
                [
                    'description' => 'Mouse',
                    'quantity' => 1,
                    'price' => 3999,
                ],
            ],
        ];
        $response = $this->cobrefacil->invoice->create($params);
        $this->assertNotNull($response['id']);
        $this->assertEquals(Invoice::PAYMENT_METHOD_PIX, $response['payable_with']);
        $this->assertEquals(Invoice::STATUS_PROCESSING, $response['status']);
    }

    public function testErrorOnCreateInvoicePayableWithBankSlipWithInvalidDueDate()
    {
        $yesterday = (new DateTime())->sub(new DateInterval('P1D'))->format('Y-m-d');
        $params = [
            'payable_with' => Invoice::PAYMENT_METHOD_BANKSLIP,
            'customer_id' => $this->getLastCustomerId(),
            'due_date' => $yesterday,
            'items' => [
                [
                    'description' => 'Teclado',
                    'quantity' => 1,
                    'price' => 4999,
                ],
                [
                    'description' => 'Mouse',
                    'quantity' => 1,
                    'price' => 3999,
                ],
            ],
            'settings' => [
                'late_fee' => [
                    'mode' => 'percentage',
                    'amount' => 10,
                ],
                'interest' => [
                    'mode' => 'daily_percentage',
                    'amount' => 0.1,
                ],
                'discount' => [
                    'mode' => 'fixed',
                    'amount' => 9.99,
                    'limit_date' => 5,
                ],
                'warning' => [
                    'description' => '- Em caso de dúvidas entre em contato com nossa Central de Atendimento.',
                ],
            ],
        ];
        try {
            $this->cobrefacil->invoice->create($params);
        } catch (InvalidParamsException $e) {
            $expectedErrors = [
                'Data de vencimento deve ser uma data maior ou igual a hoje.',
            ];
            $this->assertInvalidParamsException($expectedErrors, $e);
        }
    }

    public function testErrorOnCreateInvoicePayableWithPixWithInvalidDueDate()
    {
        $yesterday = (new DateTime())->sub(new DateInterval('P1D'))->format('Y-m-d');
        $params = [
            'payable_with' => Invoice::PAYMENT_METHOD_PIX,
            'customer_id' => $this->getLastCustomerId(),
            'due_date' => $yesterday,
            'items' => [
                [
                    'description' => 'Teclado',
                    'quantity' => 1,
                    'price' => 4999,
                ],
                [
                    'description' => 'Mouse',
                    'quantity' => 1,
                    'price' => 3999,
                ],
            ],
        ];
        try {
            $this->cobrefacil->invoice->create($params);
        } catch (InvalidParamsException $e) {
            $expectedErrors = [
                'Data de vencimento deve ser uma data maior ou igual a hoje.',
            ];
            $this->assertInvalidParamsException($expectedErrors, $e);
        }
    }

    public function testCaptureInvoicePayableWithCreditPreAuthorized()
    {
        $invoicePreAuthorized = $this->createInvoicePreAuthorized();
        $id = $invoicePreAuthorized['id'];
        $invoice = $this->cobrefacil->invoice;
        $response = $invoice->capture($id);
        $this->assertEquals("v1/invoices/$id/capture", $invoice->getLastRequestUri());
        $this->assertEquals(Invoice::STATUS_PAID, $response['status']);
        $this->assertEquals($invoicePreAuthorized['price'], $response['total_paid']);
    }

    public function testCancelInvoicePayableWithCreditPreAuthorized()
    {
        $invoicePreAuthorized = $this->createInvoicePreAuthorized();
        $response = $this->cobrefacil->invoice->cancel($invoicePreAuthorized['id']);
        $this->assertEquals(Invoice::STATUS_CANCELED, $response['status']);
        $this->assertEquals($invoicePreAuthorized['price'], $response['amount_refunded']);
    }

    public function testRefundTotalAmountOfAnInvoicePaidWithCredit()
    {
        $invoicePaidWithCredit = $this->createInvoicePaidWithCredit();
        $response = $this->cobrefacil->invoice->refund($invoicePaidWithCredit['id']);
        $this->assertEquals(Invoice::STATUS_REFUNDED, $response['status']);
        $this->assertEquals($invoicePaidWithCredit['total_paid'], $response['amount_refunded']);
    }

    public function testRefundPartialAmountOfAnInvoicePaidWithCredit()
    {
        $invoicePaidWithCredit = $this->createInvoicePaidWithCredit();
        $amountToRefund = 1.99;
        $response = $this->cobrefacil->invoice->refund($invoicePaidWithCredit['id'], $amountToRefund);
        $this->assertEquals(Invoice::STATUS_REFUNDED, $response['status']);
        $this->assertEquals($amountToRefund, $response['amount_refunded']);
    }

    public function testSearch()
    {
        $response = $this->cobrefacil->invoice->search();
        $this->assertTrue(isset($response[0]['id']));
    }

    public function testSearchWithFilter()
    {
        $filter = [
            'email' => $this->getLastInvoice()['customer']['email'],
        ];
        $response = $this->cobrefacil->invoice->search($filter);
        $this->assertGreaterThanOrEqual(1, count($response));
        foreach ($response as $invoice) {
            $this->assertEquals($filter['email'], $invoice['customer']['email']);
        }
    }

    public function testGetById()
    {
        $id = $this->getLastInvoiceId();
        $response = $this->cobrefacil->invoice->getById($id);
        $this->assertEquals($id, $response['id']);
    }

    public function testErrorOnGetByInvalidId()
    {
        $id = 'invalid';
        $invoice = $this->cobrefacil->invoice;
        try {
            $invoice->getById('invalid');
        } catch (ResourceNotFoundException $e) {
            $this->assertInvoiceNotFound($id, $invoice, $e);
        }
    }

    public function testCancelBankSlip()
    {
        $response = $this->cobrefacil->invoice->cancel($this->getLastInvoiceId(['status' => Invoice::STATUS_PENDING]));
        $this->assertEquals(Invoice::STATUS_CANCELED, $response['status']);
    }

    public function testErrorOnCancelBankSlipWithInvalidId()
    {
        $id = 'invalid';
        $invoice = $this->cobrefacil->invoice;
        try {
            $invoice->cancel('invalid');
        } catch (ResourceNotFoundException $e) {
            $this->assertInvoiceNotFound($id, $invoice, $e);
        }
    }

    public function testErrorOnCancelBankSlipAlreadyCanceled()
    {
        try {
            $this->cobrefacil->invoice->cancel($this->getLastInvoiceId(['status' => Invoice::STATUS_CANCELED]));
        } catch (InvalidParamsException $e) {
            $this->assertEquals('Somente faturas pendentes ou pré autorizadas podem ser canceladas.', $e->getMessage());
        }
    }

    protected function getLastInvoiceId(array $filter = null): string
    {
        return $this->getLastInvoice($filter)['id'];
    }

    protected function getLastInvoice(array $filter = null): array
    {
        return $this->cobrefacil->invoice->search($filter)[0];
    }

    private function createInvoicePreAuthorized(): array
    {
        $params = [
            'payable_with' => Invoice::PAYMENT_METHOD_CREDIT,
            'customer_id' => $this->getLastCustomerId(),
            'capture' => 0,
            'credit_card' => [
                'name' => 'João da Silva',
                'number' => Card::MAGIC_NUMBER_TO_APPROVE_VISA,
                'expiration_month' => '12',
                'expiration_year' => '2022',
                'security_code' => '123',
            ],
            'items' => [
                [
                    'description' => 'Teclado',
                    'quantity' => 1,
                    'price' => 4999,
                ],
                [
                    'description' => 'Mouse',
                    'quantity' => 1,
                    'price' => 3999,
                ],
            ],
        ];
        $invoice = $this->cobrefacil->invoice;
        $response = $invoice->create($params);
        $this->waitAsyncRequestBeProcessed();
        return $invoice->getById($response['id']);
    }

    private function createInvoicePaidWithCredit(): array
    {
        $params = [
            'payable_with' => Invoice::PAYMENT_METHOD_CREDIT,
            'customer_id' => $this->getLastCustomerId(),
            'capture' => 1,
            'credit_card' => [
                'name' => 'João da Silva',
                'number' => Card::MAGIC_NUMBER_TO_APPROVE_VISA,
                'expiration_month' => '12',
                'expiration_year' => '2022',
                'security_code' => '123',
            ],
            'items' => [
                [
                    'description' => 'Teclado',
                    'quantity' => 1,
                    'price' => 4999,
                ],
                [
                    'description' => 'Mouse',
                    'quantity' => 1,
                    'price' => 3999,
                ],
            ],
        ];
        $invoice = $this->cobrefacil->invoice;
        $response = $invoice->create($params);
        $this->waitAsyncRequestBeProcessed();
        return $invoice->getById($response['id']);
    }

    private function assertInvoiceNotFound(string $id, Invoice $invoice, ResourceNotFoundException $exception)
    {
        $this->assertEquals("v1/invoices/$id", $invoice->getUri());
        $this->assertResourceNotFoundException($exception, 'Cobrança não encontrada.');
    }
}
