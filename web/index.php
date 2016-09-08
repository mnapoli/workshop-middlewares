<?php

use Zend\Diactoros\Response\SapiEmitter;

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Serve static files
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']))) {
    return false;
}
require_once __DIR__ . '/../vendor/autoload.php';

// ---------------------------------------------------------------------------------------------------------------------


$application = function () {
    // Write your application: return a response
    /**
     * @see \Zend\Diactoros\Response\TextResponse
     * @see \Zend\Diactoros\Response\HtmlResponse
     * @see \Zend\Diactoros\Response\JsonResponse
     */
};


// ---------------------------------------------------------------------------------------------------------------------

// Run the application
$response = $application();
// Emit the response (with header() and echo)
(new SapiEmitter)->emit($response);
