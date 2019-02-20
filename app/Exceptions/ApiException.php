<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    private $status;

    public function __construct($message, $status)
    {
        $this->status = $status;
        parent::__construct($message);
    }

    public function getStatus()
    {
        return $this->status;
    }
}