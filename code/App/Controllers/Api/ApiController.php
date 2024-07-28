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
        $this->outputError( new \Exception('Method not implemented', 501 ) );
    }
    
    public function methodPost(): void
    {
        $this->outputError( new \Exception('Method not implemented', 501 ) );
    }
    
    public function methodPut(): void
    {
        $this->outputError( new \Exception('Method not implemented', 501 ) );
    }
    
    public function methodDelete(): void
    {
        $this->outputError( new \Exception('Method not implemented', 501 ) );
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
            $this->outputError( $exception );
        }
    }
    
    protected function outputError( \Exception $exception ): void
    {
        $view = new JsonView();
        $view->render(
            [
                'error'   => true,
                'message' => $exception->getMessage(),
            ],
            $exception->getCode()
        );
    }
    
    /**
     * @param EntityObject|EntityObject[] $data
     * @param string                      $message
     *
     * @return void
     * @throws \JsonException
     */
    protected function outputSuccess( array|EntityObject $data, string $message = '' ): void
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
                'error'   => false,
                'message' => $message,
                'count'   => count( $data ),
                'data'    => $data,
            ],
            200
        );
    }
}