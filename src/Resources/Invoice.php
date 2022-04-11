<?php

namespace CobreFacil\Resources;

use CobreFacil\ApiOperations\Create;
use CobreFacil\ApiOperations\Get;
use CobreFacil\ApiOperations\Search;
use CobreFacil\ApiOperations\Update;
use CobreFacil\Exceptions\ResourceException;

class Invoice extends ApiResource
{
    use Create;
    use Get;
    use Search;
    use Update;

    const STATUS_PROCESSING = 'processing';
    const STATUS_PENDING = 'pending';
    const STATUS_PRE_AUTHORIZED = 'pre_authorized';
    const STATUS_PAID = 'paid';
    const STATUS_DECLINED = 'declined';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_CANCELED = 'canceled';
    const STATUS_REVERSED = 'reversed';
    const STATUS_DISPUTE = 'dispute';
    const STATUS_CHARGED_BACK = 'charged_back';
    const STATUS_MANUAL_PAYMENT = 'manual_payment';
    const PAYMENT_METHOD_BANKSLIP = 'bankslip';
    const PAYMENT_METHOD_CREDIT = 'credit';
    const PAYMENT_METHOD_PIX = 'pix';

    protected $endpoint = 'invoices';

    /**
     * @throws ResourceException
     */
    public function cancel(string $id): array
    {
        return $this->setId($id)->delete();
    }

    /**
     * @throws ResourceException
     */
    public function refund(string $id, float $amount = null): array
    {
        $params = empty($amount) ? null : ['amount' => $amount];
        return $this->setId($id)->post($params, 'refund');
    }
}
