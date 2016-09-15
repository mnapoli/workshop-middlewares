<?php

namespace Test\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Superpress\Middleware\Router;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\TextResponse;
use Zend\Diactoros\ServerRequest;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function routes_request_to_controller()
    {
        $calls = 0;
        $router = new Router([
            '/' => function () {
                return new Response;
            },
            '/foo' => function () use (&$calls) {
                $calls++;
                return new Response;
            },
            '/bar' => function () {
                return new Response;
            },
        ]);

        $request = new ServerRequest([], [], '/foo');
        $next = function () {
            throw new \Exception('No route matched');
        };
        $router($request, $next);

        $this->assertEquals(1, $calls);
    }

    /**
     * @test
     */
    public function calls_handler_with_middleware_parameters()
    {
        $next = function () {
            return new TextResponse('Hello world!');
        };
        $router = new Router([
            '/' => function (ServerRequestInterface $request, callable $next) {
                return $next($request);
            },
        ]);
        $response = $router->__invoke(new ServerRequest, $next);
        $this->assertEquals('Hello world!', $response->getBody()->getContents());
    }

    /**
     * @test
     */
    public function calls_next_middleware_if_no_route_matched()
    {
        $next = function () {
            return new TextResponse('Hello world!');
        };
        $router = new Router([]);
        $response = $router->__invoke(new ServerRequest, $next);
        $this->assertEquals('Hello world!', $response->getBody()->getContents());
    }
}
