# Cobre Fácil - PHP SDK

SDK oficial de integração à API da Cobre Fácil.<br>
Para ler a documentação da API acesse: https://developers.cobrefacil.com.br

## Pré requisitos

1. PHP >= 7.1
2. cURL
3. [Composer](http://getcomposer.org/)

## Instalação

A instalação é realizada via Composer através do comando:

```
composer require cobrefacil/sdk-php
```

## Autenticação

Para realizar a autenticação é necessário informar no construtor da classe `CobreFacil` o `app_id` e `secret` disponíveis no painel de sua conta.

```php
use CobreFacil;

$cobrefacil = new CobreFacil($appId, $secret);
```

Essa classe é a responsável por gerar o token que será utilizado nas requisições e facilitar o acesso aos recursos
disponíveis.

Por padrão a classe utiliza o ambiente de `produção`, caso deseje utilizar o ambiente de `sandbox` basta passar o terceiro parâmetro como `false`.

```php
$cobrefacil = new CobreFacil($appId, $secret, false);
```

## Recursos Disponíveis

Atualmente nosso SDK disponibiliza métodos para facilitar a manipulação de `Clientes`, `Cartões de Crédito` e `Cobranças`.
Para saber quais dados devem ser enviados e recebidos consulte a [documentação da API](https://developers.cobrefacil.com.br).

Os dados devem ser enviados em forma de array associativo:

```php
$params = [
    'customer_id' => 'Y73MNPGJ18Y18V5KQODX',
    'payable_with' => 'bankslip',
    //...
];
$response = $cobrefacil->invoice->create($params);
```

E as respostas também são no formato de array associativo, exemplo usando a variável `$response` do exemplo anterior:

```php
[
    'id' => '2KD9LGERW897NZ6JM5V4',
    'customer_id' => 'Y73MNPGJ18Y18V5KQODX',
    'payable_with' => 'bankslip',
    //...
];
```



### Clientes

https://developers.cobrefacil.com.br/#clientes

```php
// POST /customers
$cobrefacil->customer->create($params);

// PUT /customers/{id}
$cobrefacil->customer->update($id, $params);

// GET /customers
$cobrefacil->customer->search();

// GET /customers?email=exemplo@mail.com
$cobrefacil->customer->search(['email' => 'exemplo@mail.com']);

// GET /customers/{id}
$cobrefacil->customer->getById($id);

// DELETE /customers/{id}
$cobrefacil->customer->remove($id);
```

### Cartões de Crédito

https://developers.cobrefacil.com.br/#cartao-de-credito

```php
// POST /cards
$cobrefacil->card->create($params);

// POST /cards/{id}/default
$cobrefacil->card->setDefault($id);

// GET /cards/{id}
$cobrefacil->card->getById($id);

// GET /cards
$cobrefacil->card->search();

// GET /cards?customer_id=Z8J53ROD2JK1PQ47WG0E
$cobrefacil->card->search(['customer_id' => 'Z8J53ROD2JK1PQ47WG0E']);

// DELETE /cards/{id}
$cobrefacil->card->remove($id);
```

### Cobranças

https://developers.cobrefacil.com.br/#cobrancas

```php
// POST /invoices
$cobrefacil->invoice->create($params);

// POST /invoices/{id}/capture
$cobrefacil->invoice->capture($id, $amount);

// GET /invoices
$cobrefacil->invoice->search();

// GET /invoices?status=paid
$cobrefacil->invoice->search(['status' => 'paid']);

// POST /invoices/{id}/refund
$cobrefacil->invoice->refund($id, $amount);

// DELETE /invoices/{id}
$cobrefacil->invoice->cancel($id);
```

## Tratamento de erros

https://developers.cobrefacil.com.br/#erros

Caso aconteça algum erro durante uma requisição, será retornada uma `exception`.

E para facilitar o tratamento dos erros disponibilizamos 3 exceptions que facilitam identificar qual foi o tipo de erro.

Erro na autenticação:

```php
use CobreFacil\Exceptions\InvalidCredentialsException;

try {
    $cobrefacil = new CobreFacil($appId, $secret);
} catch (InvalidCredentialsException $e) {
    // 401 Unauthorized: As credenciais são inválidas
}
```

Erro na requisição:

```php
use CobreFacil\Exceptions\InvalidParamsException;
use CobreFacil\Exceptions\ResourceNotFoundException;

try {
    $cobrefacil->customer->create($params);
} catch (InvalidParamsException $e) {
    // 400 Bad Request: Algum parâmetro obrigatório não foi enviado ou é inválido
    $e->getErrors();// retorna um array contendo os erros
} catch (ResourceNotFoundException $e) {
    // 404 Not Found: O registro solicitado não existe
}
```

## Webhooks

https://developers.cobrefacil.com.br/#webhooks

Para facilitar o tratamento dos eventos de webhook recebidos, disponibilizamos a classe `WebhookEvent`.

```php
$receivedEvent = WebhookEvent::createFromJson($json);
```

Com ela é possível utilizar métodos para auxiliar na leitura dos dados:

```php
$receivedEvent->getEvent();// nome do evento recebido, exemplo: invoice.created
$receivedEvent->getData();// dados da cobrança em forma de array associativo
```

E também métodos para fazer algumas verificações:

```php
$receivedEvent->isInvoiceCreated();// invoice.created
$receivedEvent->isInvoiceViewed();// invoice.viewed
$receivedEvent->isInvoiceReversed();// invoice.reversed
$receivedEvent->isInvoiceDeclined();// invoice.declined
$receivedEvent->isInvoicePreAuthorized();// invoice.pre_authorized
$receivedEvent->isInvoicePaid();// invoice.paid
$receivedEvent->isInvoiceRefunded();// invoice.refunded
$receivedEvent->isInvoiceCanceled();// invoice.canceled
$receivedEvent->isInvoiceDispute();// invoice.dispute
$receivedEvent->isInvoiceDisputeSucceeded();// invoice.dispute_succeeded
$receivedEvent->isInvoiceDisputeChargedBack();// invoice.charged_back
```
