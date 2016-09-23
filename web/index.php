<?php

use Superpress\Container;
use Superpress\Middleware\ErrorHandler;
use Superpress\Middleware\HttpBasicAuthentication;
use Superpress\Middleware\Pipe;
use Superpress\Middleware\Router;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
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
    new Router([
        '/' => function () use ($container) {
            $twig = $container->twig();
            $latestArticles = $container->articleRepository()->getArticles();
            return new HtmlResponse($twig->render('home.html.twig', [
                'articles' => $latestArticles,
            ]));
        },
        '/about' => function () use ($container) {
            return new HtmlResponse($container->twig()->render('about.html.twig'));
        },
        '/api/{path}' => new Pipe([
            new HttpBasicAuthentication(['user' => 'password']),
            new Router([
                '/api/articles' => function () use ($container) {
                    return new JsonResponse($container->articleRepository()->getArticles());
                },
                '/api/time' => function () {
                    return new JsonResponse(time());
                },
            ]),
        ]),
    ]),
]);


// ---------------------------------------------------------------------------------------------------------------------

// Run the application
$lastFallback = function () {
    return new TextResponse('Page not found', 404);
};
$response = $application(ServerRequestFactory::fromGlobals(), $lastFallback);
// Emit the response (with header() and echo)
(new SapiEmitter)->emit($response);
