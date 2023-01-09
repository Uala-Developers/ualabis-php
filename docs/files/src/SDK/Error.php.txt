<?php

namespace Uala;

use Exception;

class Error extends Exception
{
    protected $message;
    protected $errorType;
    protected $statusCode;

    const ERRORS_TYPE = [
        666 => 'unknown',
        999 => 'api_error',
        1000 => 'input_validation',
        1001 => 'required_field_empty',
        1002 => 'invalid_credentials',
        1003 => 'invalid_amount',
        1004 => 'user_no_exist',
        1005 => 'no_owner_order',
        1006 => 'no_record_found',
        2004 => 'api_error',
        2005 => 'no_owner_order',
        3001 => 'required_field_empty',
        3003 => 'user_no_exist',
        3005 => 'invalid_client_id',
        3006 => 'invalid_client_secret',
    ];

    public function __construct($message, $errorCode, $statusCode)
    {
        $this->message = $message;
        $this->errorType = self::ERRORS_TYPE[$errorCode];
        $this->statusCode = $statusCode;
    }

    final public function getErrorType()
    {
        return $this->errorType;
    }

    final public function getStatusCode()
    {
        return $this->statusCode;
    }
}
