<?php

use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\Response\TextResponse;
use Zend\Diactoros\ServerRequestFactory;

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Serve static files
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']))) {
    return false;
}
require_once __DIR__ . '/../vendor/autoload.php';

// ---------------------------------------------------------------------------------------------------------------------


$application = function (ServerRequestInterface $request, callable $next) {
    $queryParams = $request->getQueryParams();
    $name = !empty($queryParams['name']) ? $queryParams['name'] : 'world';
    return new TextResponse('Hello ' . $name . '!');
};


// ---------------------------------------------------------------------------------------------------------------------

// Run the application
$lastFallback = function () {
    return new TextResponse('Page not found', 404);
};
$response = $application(ServerRequestFactory::fromGlobals(), $lastFallback);
// Emit the response (with header() and echo)
(new SapiEmitter)->emit($response);
