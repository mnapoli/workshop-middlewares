<?php

namespace Superpress\Middleware;

use DI\Container;
use Psr\Http\Message\ServerRequestInterface;
use Superpress\Middleware;

/**
 * The middleware pipe will execute the given middlewares in order, handling
 * passing the correct $next variable to each.
 */
class Pipe implements Middleware
{
    /**
     * @var Middleware[]
     */
    private $middlewares;

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container, array $middlewares)
    {
        $this->middlewares = $middlewares;
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        // Go through each middleware, from last to first
        foreach (array_reverse($this->middlewares) as $middleware) {
            $next = function (ServerRequestInterface $request) use ($middleware, $next) {
                return $this->container->call($middleware, [
                    'request' => $request,
                    'next' => $next,
                ]);
            };
        }

        // Invoke the first middleware
        return $next($request);
    }
}
