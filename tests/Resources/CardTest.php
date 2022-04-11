<?php

namespace CobreFacil\Resources;

use CobreFacil\BaseTest;
use CobreFacil\Exceptions\InvalidParamsException;
use CobreFacil\Exceptions\ResourceNotFoundException;
use Faker\Factory;
use Faker\Provider\Payment;

class CardTest extends BaseTest
{
    public function testCreate()
    {
        Payment::$expirationDateFormat = 'm/Y';
        $faker = Factory::create();
        $params = [
            'default' => 1,
            'customer_id' => $this->getLastCustomerId(),
            'name' => $faker->name,
            'number' => Card::MAGIC_NUMBER_TO_APPROVE_MASTERCARD,
            'expiration_month' => date('m'),
            'expiration_year' => date('Y'),
            'security_code' => $faker->randomNumber(3, true),
        ];
        $response = $this->cobreFacil->card()->create($params);
        $this->assertIsString($response['id']);
        $this->assertEquals($params['customer_id'], $response['customer_id']);
        $this->assertEquals($params['default'], $response['default']);
        $this->assertEquals(substr($params['number'], -4), $response['last4_digits']);
        $this->assertEquals($params['expiration_month'], $response['expiration_month']);
        $this->assertEquals($params['expiration_year'], $response['expiration_year']);
    }

    public function testErrorOnCreate()
    {
        Payment::$expirationDateFormat = 'm/Y';
        $faker = Factory::create();
        $params = [
            'default' => 1,
            'customer_id' => $this->getLastCustomerId(),
            'name' => $faker->name,
            'number' => Card::MAGIC_NUMBER_TO_APPROVE_MASTERCARD,
            'expiration_month' => '01',
            'expiration_year' => '2001',
            'security_code' => $faker->randomNumber(3, true),
        ];
        try {
            $this->cobreFacil->card()->create($params);
        } catch (InvalidParamsException $e) {
            $expectedErrors = [
                'O cartão de crédito informado está vencido.',
            ];
            $this->assertInvalidParamsException($expectedErrors, $e);
        }
    }

    public function testSearch()
    {
        $response = $this->cobreFacil->card()->search();
        $this->assertTrue(isset($response[0]['id']));
    }

    public function testSearchWithFilter()
    {
        $lastCard = $this->getLastCard();
        $expiration = $lastCard['expiration_year'] . '-' . $lastCard['expiration_month'];
        $filter = [
            'expiration' => $expiration,
        ];
        $response = $this->cobreFacil->card()->search($filter);
        $this->assertGreaterThanOrEqual(1, count($response));
        foreach ($response as $card) {
            $this->assertEquals($expiration, $card['expiration_year'] . '-' . $card['expiration_month']);
        }
    }

    public function testGetById()
    {
        $response = $this->cobreFacil->card()->getById($this->getLastCardId());
        $this->assertIsString($response['id']);
    }

    public function testErrorOnGetByInvalidId()
    {
        $id = 'invalid';
        $card = $this->cobreFacil->card();
        try {
            $card->getById($id);
        } catch (ResourceNotFoundException $e) {
            $this->assertCardNotFound($id, $card, $e);
        }
    }

    public function testSetDefault()
    {
        $id = $this->getLastCardId();
        $card = $this->cobreFacil->card();
        $response = $card->setDefault($id);
        $this->assertEquals("v1/cards/$id/default", $card->getLastRequestUri());
        $this->assertEquals(1, $response['default']);
    }

    public function testErrorOnSetDefault()
    {
        $id = 'invalid';
        $card = $this->cobreFacil->card();
        try {
            $card->setDefault($id);
        } catch (ResourceNotFoundException $e) {
            $this->assertCardNotFound($id, $card, $e);
        }
    }

    public function testRemove()
    {
        $id = $this->getLastCardId();
        $card = $this->cobreFacil->card();
        $response = $card->remove($id);
        $this->assertEquals("v1/cards/$id", $card->getLastRequestUri());
        $this->assertEquals($id, $response['id']);
        $this->assertNotNull($response['deleted_at']);
    }

    public function testErrorOnRemove()
    {
        $id = 'invalid';
        $card = $this->cobreFacil->card();
        try {
            $card->remove($id);
        } catch (ResourceNotFoundException $e) {
            $this->assertCardNotFound($id, $card, $e);
        }
    }

    private function assertCardNotFound(string $id, Card $card, ResourceNotFoundException $exception)
    {
        $this->assertEquals("v1/cards/$id", $card->getUri());
        $this->assertResourceNotFoundException($exception, 'Nenhum resultado encontrado.');
    }
}
