<?php

namespace Safronik\Routers;

class Route
{
    public string $id;
    
    private string $type;   // Controller type (web, api, ftp, cli, ... )
    private string $path;   // Controller to call
    private string $method; // Method to call in controller
    private array  $route;
    private array  $parameters; // Parameters to pass to a model
    
    public function __construct( string $type, string $method, string $path, array $parameters )
    {
        $this->type       = ucfirst( strtolower($type ) ); // cli, web, api, ftp
        $this->method     = ucfirst( strtolower( $method ) );
        $this->path       = $this->standardizePath( $path, $this->type );
        $this->route      = explode( '\\', $this->path );
        $this->parameters = $parameters;
        
        ksort($parameters );
        $this->id = md5( "$method$path" . implode('',$parameters ) );
    }
    
    private function standardizePath( string $path, string $type ): string
    {
        $path = str_replace( '/', '\\', $path );
        $path = trim( $path, '\\' );
        $path = ucwords( $path, '\\' );
        $path = $type . '\\' . $path;
        
        return $path;
    }
    
    public function pop(): string
    {
        return array_pop( $this->route );
    }
    
    public function getPath(): string
    {
        return implode( '\\', $this->route );
    }
    
    public function getType(): string
    {
        return $this->type;
    }
    
    public function getMethod(): string
    {
        return $this->method;
    }
    
    public function getRoute(): array
    {
        return $this->route;
    }
    
    public function getParameters(): array
    {
        return $this->parameters;
    }
    
    public function setPath( string $path ): void
    {
        $this->path = $path;
    }
    
    public function setMethod( string $method ): void
    {
        $this->method = $method;
    }
    
    public function setRoute( array $route ): void
    {
        $this->route = $route;
    }
}