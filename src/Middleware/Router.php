<?php

namespace Superpress\Middleware;

use DI\Container;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface;
use Superpress\Middleware;

class Router implements Middleware
{
    /**
     * @var array
     */
    private $routes;

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container, array $routes)
    {
        $this->routes = $routes;
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            foreach ($this->routes as $path => $handler) {
                $r->addRoute('GET', $path, $handler);
            }
        });

        $result = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        if ($result[0] === Dispatcher::FOUND) {
            $handler = $result[1];
            $attributes = $result[2];
            foreach ($attributes as $name => $value) {
                $request = $request->withAttribute($name, $value);
            }
            return $this->container->call($handler, [
                'request' => $request,
                'next' => $next,
            ]);
        }

        // in case of 404 or 405 (method not allowed), call the next middleware
        return $next($request);
    }
}
