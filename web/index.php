<?php

use Psr\Http\Message\ServerRequestInterface;
use Superpress\Container;
use Superpress\Middleware\ErrorHandler;
use Superpress\Middleware\Pipe;
use Zend\Diactoros\Response\HtmlResponse;
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

$container = new Container;

// ---------------------------------------------------------------------------------------------------------------------


$application = new Pipe([
    new ErrorHandler(),
    function (ServerRequestInterface $request, callable $next) use ($container) {
        $url = $request->getUri()->getPath();
        $twig = $container->twig();
        if ($url === '/') {
            $latestArticles = $container->articleRepository()->getArticles();
            return new HtmlResponse($twig->render('home.html.twig', [
                'articles' => $latestArticles,
            ]));
        } elseif ($url === '/about') {
            return new HtmlResponse($container->twig()->render('about.html.twig'));
        }
        return $next($request);
    }
]);


// ---------------------------------------------------------------------------------------------------------------------

// Run the application
$lastFallback = function () {
    return new TextResponse('Page not found', 404);
};
$response = $application(ServerRequestFactory::fromGlobals(), $lastFallback);
// Emit the response (with header() and echo)
(new SapiEmitter)->emit($response);
