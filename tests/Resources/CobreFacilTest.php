<?php

namespace CobreFacil\Resources;

use CobreFacil\BaseTest;
use Exception;

class CobreFacilTest extends BaseTest
{
    public function testMagicMethodGet()
    {
        $cobrefacil = $this->createCobreFacilClient();
        $this->assertInstanceOf(Authentication::class, $cobrefacil->authentication);
        $this->assertInstanceOf(Customer::class, $cobrefacil->customer);
        $this->assertInstanceOf(Card::class, $cobrefacil->card);
        $this->assertInstanceOf(Invoice::class, $cobrefacil->invoice);
    }

    public function testError()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Nenhum recurso foi encontrado com o nome \"invalid\".");
        $cobrefacil = $this->createCobreFacilClient();
        $cobrefacil->invalid;
    }
}
