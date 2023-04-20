<?php
use App\Controllers\WelcomeController;
use App\Utils\CustomMethods;
use Dotenv\Dotenv;
// auto loading
require_once __DIR__ . '/vendor/autoload.php';
//.env loading
( Dotenv::createImmutable(__DIR__ ) )->load();

// URL parsing
$path = str_replace( '/index.php', '', $_SERVER['PHP_SELF'] );
// Path routing
if ( $path === '/' || empty( $path ) ){
    (new WelcomeController())->index();
} else {
    // Gets controller and action from url
    list( $root, $controllerName, $action ) = explode('/', $path);
    $controllerClass = sprintf( 'App\\Controllers\\%sController', ucfirst( $controllerName ) );
    // Calling controller and action
    if ( class_exists( $controllerClass ) ){
        $controller = new $controllerClass();
        if ( method_exists( $controller, $action ) ){
            call_user_func([$controller, $action]);
        } else {
            CustomMethods::get404();
        }
    } else {
        CustomMethods::get404();
    }
}
