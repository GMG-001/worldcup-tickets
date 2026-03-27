<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientSeatsException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Not enough seats available for the requested quantity.');
    }
}
