<?php

namespace Superpress\Middleware;

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

    public function __construct(array $routes)
    {
        $this->routes = $routes;
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
            return $handler($request, $next);
        }

        // in case of 404 or 405 (method not allowed), call the next middleware
        return $next($request);
    }
}
