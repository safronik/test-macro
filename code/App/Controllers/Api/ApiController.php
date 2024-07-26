<?php

namespace Controllers\Api;

use Controllers\Controller;
use Safronik\Helpers\ValidationHelper;
use Safronik\Models\Domains\EntityObject;
use Views\JsonView;

abstract class ApiController extends Controller{
    
    private $api_key = '12345';
    
    private static $rules = [
        'api_key' => [ 'required', 'type' => 'string', 'content' => '@^[a-zA-Z0-9]{5,64}$@', ],
    ];
    
    public function methodGet(): void
    {
        $this->outputError( 'Not implemented' );
    }
    
    public function methodPost(): void
    {
        $this->outputError( 'Not implemented' );
    }
    
    public function methodPut(): void
    {
        $this->outputError( 'Not implemented' );
    }
    
    public function methodDelete(): void
    {
        $this->outputError( 'Not implemented' );
    }
    
    protected function init(): void
    {
        $this->checkApiKey();
    }
    
    private function checkApiKey( $api_key = null )
    {
        try{
            ValidationHelper::validate(
                $api_key
                    ? [ 'api_key' => $api_key ]
                    : $this->request->parameters,
                self::$rules
            );
            
            $this->api_key !== $this->request->parameters['api_key'] &&
                throw new \Exception( 'Wrong api key');
            
        }catch( \Exception $exception ){
            $this->outputError( $exception->getMessage() );
        }
    }
    
    protected function outputError( $message, $code = 0 ): void
    {
        $view = new JsonView();
        $view->render(
            [
                'eroror'  => $code,
                'message' => $message,
            ],
            402
        );
    }
    
    /**
     * @param EntityObject|EntityObject[] $data
     * @param                             $message
     * @param                             $code
     *
     * @return void
     * @throws \JsonException
     */
    protected function outputSuccess( array|EntityObject $data, $message = '', $code = 0 ): void
    {
        $data = is_array( $data ) ? $data : [ $data ];
        
        foreach( $data as &$datum ){
            if( $datum instanceof EntityObject ){
                $datum = $datum->toArray();
            }
        }
        unset( $datum );
        
        $view = new JsonView();
        $view->render(
            [
                'success' => true,
                'message' => $message,
                'count'   => count( $data ),
                'data'    => $data,
            ],
            200
        );
    }
}