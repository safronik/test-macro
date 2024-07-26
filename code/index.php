<?php
    
    use Safronik\Routers\Request;
    
    // Develop mode
    error_reporting( E_ALL ^ E_DEPRECATED );
    ini_set( 'display_errors', 'On' );

    require_once 'vendor/autoload.php';
    require_once 'autoloader.php';
    
    $db = \Safronik\DB\DB::getInstance(
        new \Safronik\DB\DBConfig( [
            'driver'   => 'pdo',
            'username' => 'root',
            'password' => 'root',
            'hostname' => 'test-db', // or could be a container name if you are using Docker
            'database' => 'test',
        ] )
    );
    
    $router = new \Safronik\Routers\Router( 'Controllers' );
    $router->findController() && $router->executeRoute();