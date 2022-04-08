<?php

namespace CobreFacil\Resources;

use CobreFacil\ApiOperations\Create;
use CobreFacil\ApiOperations\Get;
use CobreFacil\ApiOperations\Remove;
use CobreFacil\ApiOperations\Search;
use CobreFacil\ApiOperations\Update;

class Invoice extends ApiResource
{
    use Create;
    use Get;
    use Remove;
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
}
