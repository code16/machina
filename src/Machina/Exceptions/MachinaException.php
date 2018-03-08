<?php

namespace Code16\Machina\Exceptions;

use Exception;

abstract class MachinaException extends Exception
{
    /**
     * @var integer
     */
    protected $code = 500;
}
