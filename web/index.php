<?php

use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\Response\TextResponse;
use Zend\Diactoros\ServerRequestFactory;

// Serve static files
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']))) {
    return false;
}
require_once __DIR__ . '/../vendor/autoload.php';

// ---------------------------------------------------------------------------------------------------------------------


$application = function (ServerRequestInterface $request) {
    return new TextResponse('Hello world!');
};


// ---------------------------------------------------------------------------------------------------------------------

// Run the application
$response = $application(ServerRequestFactory::fromGlobals());
// Emit the response (with header() and echo)
(new SapiEmitter)->emit($response);
