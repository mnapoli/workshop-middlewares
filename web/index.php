<?php

use DI\ContainerBuilder;
use Psr\Http\Message\ServerRequestInterface;
use Superpress\Blog\ArticleRepository;
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

$container = ContainerBuilder::buildDevContainer();
$container->set(Twig_Environment::class, function () {
    $loader = new Twig_Loader_Filesystem(__DIR__ . '/../src/Views');
    return new Twig_Environment($loader, [
        'debug' => true,
        'cache' => false,
        'strict_variables' => false,
    ]);
});
$container->set(HttpBasicAuthentication::class, function () {
    return new HttpBasicAuthentication(['user' => 'password']);
});

// ---------------------------------------------------------------------------------------------------------------------


$application = new Pipe([
    new ErrorHandler(),
    new Router([
        '/' => function () use ($container) {
            $twig = $container->get(Twig_Environment::class);
            $latestArticles = $container->get(ArticleRepository::class)->getArticles();
            return new HtmlResponse($twig->render('home.html.twig', [
                'articles' => $latestArticles,
            ]));
        },
        '/about' => function () use ($container) {
            return new HtmlResponse($container->get(Twig_Environment::class)->render('about.html.twig'));
        },
        '/api/{path}' => new Pipe([
            new HttpBasicAuthentication(['user' => 'password']),
            new Router([
                '/api/articles' => function () use ($container) {
                    return new JsonResponse($container->get(ArticleRepository::class)->getArticles());
                },
                '/api/time' => function () {
                    return new JsonResponse(time());
                },
                '/api/whoami' => function (ServerRequestInterface $request) {
                    return new JsonResponse($request->getAttribute('user'));
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
