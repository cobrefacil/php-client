<?php

namespace CobreFacil;

class WebhookTest extends BaseTest
{
    /**
     * @dataProvider dataProvider
     */
    public function testIsInvoiceCreated($event, $method)
    {
        $json = $this->getJson($event);
        $jsonDecoded = json_decode($json, true);
        $receivedEvent = WebhookEvent::createFromJson($json);
        $this->assertEquals($event, $receivedEvent->getEvent());
        $this->assertTrue($receivedEvent->$method());
        $this->assertEquals($jsonDecoded['data'], $receivedEvent->getData());
    }

    public function dataProvider()
    {
        return [
            [WebhookEvent::INVOICE_CREATED, 'isInvoiceCreated'],
            [WebhookEvent::INVOICE_VIEWED, 'isInvoiceViewed'],
            [WebhookEvent::INVOICE_REVERSED, 'isInvoiceReversed'],
            [WebhookEvent::INVOICE_DECLINED, 'isInvoiceDeclined'],
            [WebhookEvent::INVOICE_PRE_AUTHORIZED, 'isInvoicePreAuthorized'],
            [WebhookEvent::INVOICE_PAID, 'isInvoicePaid'],
            [WebhookEvent::INVOICE_REFUNDED, 'isInvoiceRefunded'],
            [WebhookEvent::INVOICE_CANCELED, 'isInvoiceCanceled'],
            [WebhookEvent::INVOICE_DISPUTE, 'isInvoiceDispute'],
            [WebhookEvent::INVOICE_DISPUTE_SUCCEEDED, 'isInvoiceDisputeSucceeded'],
            [WebhookEvent::INVOICE_CHARGED_BACK, 'isInvoiceChargedBack'],
        ];
    }

    private function getJson(string $event): string
    {
        return '{
            "event": "' . $event . '",
            "data": {
                "id": "PQ9RDZ2376N7YMGJ8V1K",
                "payable_with": "bankslip",
                "due_date": "2020-12-15",
                "price": 49.9,
                "fine_delay": null,
                "total_paid": null,
                "amount_released": null,
                "fee": null,
                "paid_at": null,
                "payment_method": null,
                "document_number": null,
                "barcode": "34191090570137951893731339210002284700000004990",
                "barcode_data": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAZUAAAAyCAYAAACUNUbZAAACHUlEQVR42u3V2wqAIAwA0P3/Txc9%0ABD6kuGkQcYIIb63W7EREHNfRXu+z126PUf9b657Gs/OycXrPPZo3u/5L93nqH9VDJS+7xnfX2Y78%0AVOq0uu9m5vfijdrV/Zep02q8mbytfJdM/Oy+XHmfal1V/4cr+Q+oQAUqUIEKVKACFahABSpQgQpU%0AoAIVqEAFKlCBClSgAhWoQAUqUIEKVKACFahABSpQgQpUoAIVqEAFKlCBClSgAhWoQAUqUIEKVKAC%0AFahABSpQgQpUoAIVqEAFKlCBClSgAhWoQAUqUIEKVKACFahABSpQgQpUoAIVqEAFKlCBClSgAhWo%0AQAUqUIEKVKACFahABSpQgQpUoAIVqEAFKlCBClSgAhWoQAUqUIEKVKACFahABSpQgQpUoAIVqEAF%0AKlCBClSgAhWoQAUqUIEKVKACFahABSpQgQpUoAIVqEAFKlCBClSgAhWoQAUqUIEKVKACFahABSpQ%0AgQpUoAIVqEAFKlCBClSgAhWoQAUqUIEKVKACFahABSpQgQpUoAIVqEAFKlCBClSgAhWoQAUqUIEK%0AVKACFahABSpQgQpUoAIVqEAFKlCBClSgAhWoQAUqUIEKVKACFahABSpQgQpUoAIVqEAFKlCBClSg%0AAhWoQAUqUIEKVKACFahABSpQgQpUoAIVqEAFKlCBClSgAhWoQAUqUIEKVKACFahABSpQgQpUoAIV%0AqEAFKv9G5QSDfSad5j+EigAAAABJRU5ErkJggg==%0A",
                "status": "pending",
                "settings": null,
                "items": [],
                "customer": {
                    "id": "Z8J53ROD2JK1PQ47WG0E",
                    "person_type": 2,
                    "ein": null,
                    "company_name": "COBRE FACIL LTDA ME",
                    "taxpayer_id": "12345678909",
                    "personal_name": null,
                    "telephone": "11988887777",
                    "cellular": "11988887777",
                    "email": "exemplo@cobrefacil.com.br",
                    "email_cc": null,
                    "full_name": "COBRE FACIL LTDA ME",
                    "document": "12345678909",
                    "address": {
                        "id": "9W2LVGOPRJMR780DEJK1",
                        "description": "Endereço principal",
                        "zipcode": "01311000",
                        "street": "Avenida Paulista",
                        "number": "807",
                        "complement": "A813 CJ 2315",
                        "neighborhood": "Bela Vista",
                        "city": "São Paulo",
                        "state": "SP",
                        "created_at": "2020-04-03T11:31:52+00:00",
                        "updated_at": "2020-04-03T11:31:52+00:00",
                        "deleted_at": null
                    },
                    "created_at": "2020-04-03T11:31:52+00:00",
                    "updated_at": "2020-04-03T11:31:52+00:00",
                    "deleted_at": null
                },
                "created_at": "2020-04-03T13:54:59+00:00",
                "updated_at": "2020-04-03T13:54:59+00:00"
            }
        }';
    }
}
