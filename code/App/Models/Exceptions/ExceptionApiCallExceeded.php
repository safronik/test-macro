<?php

namespace Models\Exceptions;

class ExceptionApiCallExceeded extends ModelException{
    
    /**
     * @param string $string
     * @param int    $int
     */
    public function __construct( string $message, int $code )
    {
        parent::__construct( $message, $code );
    }
}