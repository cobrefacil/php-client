<?php

namespace CobreFacil\Resources;

use CobreFacil\Exceptions\ResourceException;

class Invoice extends ApiResource
{
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
    public function list(?array $filter = null): array
    {
        return $this->getRequest($filter);
    }
    /**
     * @throws ResourceException
     */
    public function getById(string $id): array
    {
        return $this->setId($id)->getRequest();
    }

    /**
     * @throws ResourceException
     */
    public function create(array $params): array
    {
        return $this->postRequest($params);
    }

    /**
     * @throws ResourceException
     */
    public function delete(string $id): array
    {
        return $this->setId($id)->deleteRequest();
    }
}