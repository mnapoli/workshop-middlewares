<?php
declare(strict_types = 1);

namespace Superpress\Middleware;

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

    public function __construct(array $middlewares)
    {
        $this->middlewares = $middlewares;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        // Go through each middleware, from last to first
        foreach (array_reverse($this->middlewares) as $middleware) {
            $next = function (ServerRequestInterface $request) use ($middleware, $next) {
                return $middleware($request, $next);
            };
        }

        // Invoke the first middleware
        return $next($request);
    }
}
