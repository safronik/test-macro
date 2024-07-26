<?php

namespace Controllers;

use Safronik\Routers\Request;
use Safronik\Routers\Route;

abstract class Controller{
    
    protected Route   $route;
    protected Request $request;
    
    public function __construct( Route $route )
    {
        $this->request = Request::getInstance();
        $this->route   = $route;
        $this->init();
    }
    
    abstract protected function init(): void;
    
    public function getAvailableRotes(): array
    {
        $routes = [];
        
        $reflection = new \ReflectionClass( $this );
        
        foreach( $reflection->getMethods() as $method ){
            
            if( $method->getName() === 'getAvailableRotes' ){
                continue;
            }
            
            if( $method->isPublic() &&
                (
                    str_starts_with( $method->getName(), 'method' ) ||
                    str_starts_with( $method->getName(), 'action' )
                )
            ){
                $routes[] = $method->getName();
            }
        }
        
        return $routes;
    }
}