<?php

namespace Code16\Machina\Exceptions;

use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class MissingTokenException extends JWTException
{
    protected $statusCode = 400;
}
