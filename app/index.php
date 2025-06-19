<?php

require_once 'controller/ErrorController.php';

use Controller\ErrorController;

define('ENV', parse_ini_file('../.env'));

$parse = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url = ltrim($parse, '/');
$urlExploded = explode('/', $url);
$controller = isset($urlExploded[0]) ? ucfirst($urlExploded[0]) : null;
$action = $urlExploded[1] ?? null;
$value = $urlExploded[2] ?? null;
$routes = require_once 'config/routes.php';

try {
    if (! array_key_exists($controller, $routes) || ! in_array($action, $routes[$controller])) {
        throw new \Exception('Endpoint não encontrado.', 404);
    }

    require_once 'controller/' . $controller . 'Controller.php';

    $className = 'Controller\\' . $controller . 'Controller';
    $controllerClass = new $className();
    $response = $controllerClass->$action($value);
} catch (\Exception $e) {
    $errorController = new ErrorController($e);
    $response = $errorController->show();
}

http_response_code((int) $response['code']);
echo json_encode($response);
