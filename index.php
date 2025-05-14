<?php
session_start();
require_once './vendor/autoload.php';

header('Access-Control-Allow-Origin: https://account.myriware.space');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->get( '/', '/landing.html');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $_GET['err_code'] = '404';
        include "./static/error.php";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        $_GET['err_code'] = '405';
        include "./static/error.php";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        foreach (array_keys($vars) as $key) {
            $_GET[$key] = $vars[$key];
        }
        if ($handler == "SESSION_INFO") {
            header('Content-Type: application/json');
            echo json_encode($_SESSION);
        } else {
            include ".$handler";
        }
        break;
}