<?php

namespace CobreFacil\Resources;

use CobreFacil\BaseTest;
use CobreFacil\Exceptions\InvalidParamsException;
use CobreFacil\Exceptions\ResourceNotFoundException;
use Exception;
use Faker\Factory;
use Faker\Provider\pt_BR\Person;
use Faker\Provider\pt_BR\PhoneNumber;

class CustomerTest extends BaseTest
{
    public function testCreate()
    {
        $faker = Factory::create();
        $phoneNumber = new PhoneNumber($faker);
        $params = [
            'person_type' => '1',
            'taxpayer_id' => (new Person($faker))->cpf(false),
            'personal_name' => $faker->name,
            'telephone' => $phoneNumber->cellphoneNumber(false),
            'cellular' => $phoneNumber->cellphoneNumber(false),
            'email' => $faker->email,
            'email_cc' => $faker->email,
            'address' => [
                'description' => 'AP',
                'zipcode' => '01311000',
                'street' => 'Avenida Paulista',
                'number' => '123',
                'complement' => 'Ap 42',
                'neighborhood' => 'Bela Vista',
                'city' => 'São Paulo',
                'state' => 'SP',
            ],
        ];
        $response = $this->cobreFacil->customer()->create($params);
        $this->assertTrue(isset($response['id']));
        $this->assertEquals($params['person_type'], $response['person_type']);
        $this->assertEquals($params['taxpayer_id'], $response['taxpayer_id']);
        $this->assertEquals($params['personal_name'], $response['personal_name']);
        $this->assertEquals($params['cellular'], $response['cellular']);
        $this->assertEquals($params['person_type'], $response['person_type']);
        $this->assertEquals($params['email'], $response['email']);
        $this->assertEquals($params['email_cc'], $response['email_cc']);
        $this->assertEquals($params['address']['description'], $response['address']['description']);
        $this->assertEquals($params['address']['zipcode'], $response['address']['zipcode']);
        $this->assertEquals($params['address']['street'], $response['address']['street']);
        $this->assertEquals($params['address']['number'], $response['address']['number']);
        $this->assertEquals($params['address']['complement'], $response['address']['complement']);
        $this->assertEquals($params['address']['neighborhood'], $response['address']['neighborhood']);
        $this->assertEquals($params['address']['city'], $response['address']['city']);
        $this->assertEquals($params['address']['state'], $response['address']['state']);
    }

    public function testErrorOnCreate()
    {
        try {
            $faker = Factory::create();
            $phoneNumber = new PhoneNumber($faker);
            $this->cobreFacil->customer()->create([
                'person_type' => '1',
                'taxpayer_id' => (new Person($faker))->cpf(false),
                'personal_name' => $faker->name,
                'telephone' => $phoneNumber->cellphoneNumber(false),
                'cellular' => $phoneNumber->cellphoneNumber(false),
                'email' => $faker->email,
                'email_cc' => $faker->email,
            ]);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidParamsException::class, $e);
            $errors = $e->getErrors();
            $expectedErrors = [
                'O campo address é obrigatório.',
            ];
            $this->assertEquals($expectedErrors, $errors);
        }
    }

    public function testList()
    {
        $response = $this->cobreFacil->customer()->list();
        $this->assertTrue(isset($response[0]['id']));
    }

    public function testListWithFilter()
    {
        $filter = [
            'email' => $this->getFirstCustomer()['email'],
        ];
        $response = $this->cobreFacil->customer()->list($filter);
        $this->assertEquals($response[0]['email'], $filter['email']);
    }

    public function testGetById()
    {
        $response = $this->cobreFacil->customer()->getById($this->getValidId());
        $this->assertIsString($response['id']);
    }

    public function testErrorOnGetByInvalidId()
    {
        $id = 'invalid';
        $customer = $this->cobreFacil->customer();
        try {
            $customer->getById($id);
        } catch (Exception $e) {
            $this->assertEquals("v1/customers/$id", $customer->getUri());
            $this->assertResourceNotFoundException($e);
        }
    }

    public function testUpdate()
    {
        $faker = Factory::create();
        $customerToUpdate = $this->getFirstCustomer();
        $params = $customerToUpdate;
        $params['personal_name'] = $faker->name;
        $response = $this->cobreFacil->customer()->update($customerToUpdate['id'], $params);
        $this->assertEquals($params['personal_name'], $response['personal_name']);
    }

    public function testErrorOnUpdate()
    {
        $id = 'invalid';
        $params = $this->getFirstCustomer();
        $customer = $this->cobreFacil->customer();
        try {
            $customer->update($id, $params);
        } catch (Exception $e) {
            $this->assertEquals("v1/customers/$id", $customer->getUri());
            $this->assertResourceNotFoundException($e);
        }
    }

    public function testDelete()
    {
        $response = $this->cobreFacil->customer()->delete($this->getFirstCustomer()['id']);
        $this->assertNotNull($response['deleted_at']);
    }

    public function testErrorOnDelete()
    {
        $id = 'invalid';
        $customer = $this->cobreFacil->customer();
        try {
            $customer->delete($id);
        } catch (Exception $e) {
            $this->assertEquals("v1/customers/$id", $customer->getUri());
            $this->assertResourceNotFoundException($e);
        }
    }

    private function getValidId(): string
    {
        return $this->getFirstCustomer()['id'];
    }

    private function getFirstCustomer(): array
    {
        return $this->cobreFacil->customer()->list()[0];
    }

    private function assertResourceNotFoundException(Exception $exception)
    {
        $this->assertInstanceOf(ResourceNotFoundException::class, $exception);
        $this->assertEquals('Cliente não encontrado.', $exception->getMessage());
    }
}