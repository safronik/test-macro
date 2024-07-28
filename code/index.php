<?php
    
    use Safronik\Routers\Request;
    
    // Develop mode
    error_reporting( E_ALL ^ E_DEPRECATED );
    ini_set( 'display_errors', 'Off' );

    require_once 'vendor/autoload.php';
    require_once 'autoloader.php';
    
    $db = \Safronik\DB\DB::getInstance(
        new \Safronik\DB\DBConfig( [
            'driver'   => 'pdo',
            'username' => 'macro',
            'password' => 'macro',
            'hostname' => 'macro-db', // or could be a container name if you are using Docker
            'database' => 'macro',
        ] )
    );
    
    $router = new \Safronik\Routers\Router( 'Controllers' );
    $router->findController() && $router->executeRoute();