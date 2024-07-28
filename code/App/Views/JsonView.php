<?php

namespace Views;

use Views\BaseView;

class JsonView extends BaseView{
    
    public function init()
    {
        header( 'Content-Type: application/json' );
    }
    
    public function render( $output, $response_code = 200 )
    {
        echo json_encode( $output, JSON_THROW_ON_ERROR );
        http_response_code( $response_code );
        die;
    }
}