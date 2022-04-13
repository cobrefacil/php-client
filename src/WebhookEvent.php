<?php

namespace CobreFacil;

/**
 * @method isInvoiceCreated() bool
 * @method isInvoiceViewed() bool
 * @method isInvoiceReversed() bool
 * @method isInvoiceDeclined() bool
 * @method isInvoicePreAuthorized() bool
 * @method isInvoicePaid() bool
 * @method isInvoiceRefunded() bool
 * @method isInvoiceCanceled() bool
 * @method isInvoiceDispute() bool
 * @method isInvoiceDisputeSucceeded() bool
 * @method isInvoiceDisputeChargedBack() bool
 */
class WebhookEvent
{
    const INVOICE_CREATED = 'invoice.created';
    const INVOICE_VIEWED = 'invoice.viewed';
    const INVOICE_REVERSED = 'invoice.reversed';
    const INVOICE_DECLINED = 'invoice.declined';
    const INVOICE_PRE_AUTHORIZED = 'invoice.pre_authorized';
    const INVOICE_PAID = 'invoice.paid';
    const INVOICE_REFUNDED = 'invoice.refunded';
    const INVOICE_CANCELED = 'invoice.canceled';
    const INVOICE_DISPUTE = 'invoice.dispute';
    const INVOICE_DISPUTE_SUCCEEDED = 'invoice.dispute_succeeded';
    const INVOICE_CHARGED_BACK = 'invoice.charged_back';

    /** @var array */
    private $body;

    public function __construct(array $body)
    {
        $this->body = $body;
    }

    public static function createFromJson(string $json): WebhookEvent
    {
        return new self(json_decode($json, true));
    }

    public function getEvent(): string
    {
        return $this->body['event'];
    }

    public function getData(): array
    {
        return $this->body['data'];
    }

    public function __call(string $name, array $arguments): bool
    {
        $name = str_replace('isInvoice', 'invoice', $name);
        $name = $this->camelCase2UnderScore($name);
        $receivedEvent = str_replace('invoice_', 'invoice.', $name);
        return $this->getEvent() === $receivedEvent;
    }

    private function camelCase2UnderScore(string $string, string $separator = '_'): string
    {
        if (empty($string)) {
            return $string;
        }
        $string = lcfirst($string);
        $string = preg_replace("/[A-Z]/", $separator . "$0", $string);
        return strtolower($string);
    }
}
