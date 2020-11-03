<?php

namespace Code16\Machina\Exceptions;

use Tymon\JWTAuth\Exceptions\JWTException;

class MissingTokenException extends JWTException
{
    protected $statusCode = 400;
}
