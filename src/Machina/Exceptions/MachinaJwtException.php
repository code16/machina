<?php

namespace Code16\Machina\Exceptions;

use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Wrapper around JWTexception
 */
class MachinaJwtException extends MachinaException
{
    public function __construct(JWTException $e, int $code)
    {
        parent::__construct($e->getMessage(), $code);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json(['error' => $this->message], $this->code);
    }

}