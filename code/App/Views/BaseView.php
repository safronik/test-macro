<?php

namespace Views;

abstract class BaseView{
    
    public function __construct()
    {
        $this->init();
    }
    
    abstract function render( $output, $response_code );
}