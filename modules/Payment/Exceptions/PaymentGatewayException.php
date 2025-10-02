<?php

namespace Modules\Payment\Exceptions;

use Exception;

class PaymentGatewayException extends Exception
{
    protected ?string $gatewayCode = null;
    protected ?array $gatewayResponse = null;
    protected ?string $transactionId = null;

    public function __construct(
        string $message = "",
        int $code = 0,
        ?Exception $previous = null,
        ?string $gatewayCode = null,
        ?array $gatewayResponse = null,
        ?string $transactionId = null
    ) {
        parent::__construct($message, $code, $previous);
        
        $this->gatewayCode = $gatewayCode;
        $this->gatewayResponse = $gatewayResponse;
        $this->transactionId = $transactionId;
    }

    public function getGatewayCode(): ?string
    {
        return $this->gatewayCode;
    }

    public function getGatewayResponse(): ?array
    {
        return $this->gatewayResponse;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'gateway_code' => $this->gatewayCode,
            'gateway_response' => $this->gatewayResponse,
            'transaction_id' => $this->transactionId,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ];
    }
}
