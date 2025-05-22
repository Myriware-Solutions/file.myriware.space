<?php
session_start();
require_once './vendor/autoload.php';
require_once './files.php';

header('Access-Control-Allow-Origin: https://account.myriware.space');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->get( '/session', '/session.php');
    $r->get( '/', '/public/landing.php');
    $r->post( '/loginsys', '/loginsys.php');
    $r->get( '/console', '/private/console.php');
    $r->get( '/files', 'FILES');
    $r->post( '/command_proc', '/consolehandler.php');
    $r->get( '/download/{link:.*}', '/public/download.php');
    $r->get( '/dl', '/public/downloadhandler.php');
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
        include "./public/error.html";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        $_GET['err_code'] = '405';
        include "./public/error.html";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        foreach (array_keys($vars) as $key) {
            $_GET[$key] = $vars[$key];
        }
        switch ($handler) {
            case "FILES":
                header('Content-Type: application/json');
                if (!str_contains($_SESSION['asa'], '+#+&+^+-+$%[+#+[+[+@_(+$_&')) {
                    echo json_encode(["status" => false, "data" => null]);
                } else {
                    echo json_encode(["status" => true, "data" => FileManager::GetFiles()]);
                }
                break;
            default:
                include ".$handler";
            break;
        }
        break;
}