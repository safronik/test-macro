<?php

namespace Safronik\Routers;

/**
 * Front controller
 */
class Router
{
    private Route  $route;
    private ?Route  $alternative_route = null;
    private string $root_namespace;
    private array  $settings = [
        'behaviour' => 'rest_alternative',
    ];
    
    public function __construct( $root_namespace = '', $settings = [] )
    {
        $this->route = $this->newRoute(
            Request::getInstance()
        );
        $this->root_namespace = $root_namespace;
        $this->settings       = array_merge( $this->settings, $settings );
    }
    
    /**
     * Route fabric
     *
     * @param Request $request
     *
     * @return Route
     */
    private function newRoute( Request $request ): Route
    {
        return new Route(
            $request->getType(),
            $request->getMethod(),
            $request->getPath(),
            $request->parameters,
        );
    }
    
    public function findController(): bool
    {
        switch( $this->settings['behaviour'] ?? 'rest_alternative' ){
            
            // Immediately set 404-controller if the called controller doesn't exist
            case 'strict':
                return $this->isStrictRouteAvailable();
            
            // Reduce route every iteration until correct available controller will be found. Shows available endpoints
            case 'rest_strict':
                
                $this->isStrictRouteAvailable();
                
                return true;
            
            // Once available route is met returns list of endpoints
            case 'rest_alternative':
                
                if( $this->isStrictRouteAvailable() ){
                    return true;
                }
                
                if( $this->isAlternativeRouteAvailable() ){
                    $this->route = $this->alternative_route;
                    return true;
                }
                
                break;
        }
        
        return false;
    }
    
    public function isStrictRouteAvailable( ?Route $route = null ): bool
    {
        $route ??= $this->route;
        
        $controller = $this->root_namespace . '\\' . $route->getPath() . 'Controller';
        $method     = 'method' . $this->route->getMethod();
        
        return method_exists( $controller, $method );
    }

    public function isAlternativeRouteAvailable( ?Route $route = null ): bool
    {
        $route ??= $this->route;
        
        $this->alternative_route = clone( $route );
        $this->alternative_route->setMethod(
            $this->alternative_route->pop()
        );
        
        $controller = '\\' .$this->root_namespace . '\\' . $this->alternative_route->getPath() . 'Controller';
        $method     = 'action' . $this->alternative_route->getMethod();
        
        return method_exists( $controller, $method );
    }
    
    public function executeRoute(): void
    {
        $controller  = $this->root_namespace . '\\' . $this->route->getPath() . 'Controller';
        $method_type = $this->alternative_route ? 'action' : 'method';
        $method      = $this->route->getMethod();
        
        call_user_func([ new $controller( $this->route ), $method_type . ucfirst($method ) ] );
    }
    
    public function setDefaultRoute(): true
    {
        $this->route = new Route(
            $this->route->getType(),
            Request::getInstance()->method,
            'Default',
            Request::getInstance()->parameters,
        );
        
        return true;
    }
    
    public function getRoute(): Route
    {
        return $this->route;
    }
    public function setRoute( Route $route ): void
    {
        $this->route = $route;
    }
    
    private function getAvailableRotes()
    {
    
    }
}