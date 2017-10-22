<?php

namespace Test\Middleware;

use DI\ContainerBuilder;
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
        $container = ContainerBuilder::buildDevContainer();

        $calls = 0;
        $router = new Router($container, [
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
    public function resolves_route_attributes()
    {
        $container = ContainerBuilder::buildDevContainer();

        $router = new Router($container, [
            '/{test}' => function (ServerRequestInterface $request) {
                return new TextResponse($request->getAttribute('test'));
            },
        ]);

        $request = new ServerRequest([], [], '/foo');
        $next = function () {
            throw new \Exception('No route matched');
        };
        $response = $router->__invoke($request, $next);

        $this->assertEquals('foo', $response->getBody()->getContents());
    }

    /**
     * @test
     */
    public function calls_handler_with_middleware_parameters()
    {
        $container = ContainerBuilder::buildDevContainer();

        $next = function () {
            return new TextResponse('Hello world!');
        };
        $router = new Router($container, [
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
        $container = ContainerBuilder::buildDevContainer();

        $next = function () {
            return new TextResponse('Hello world!');
        };
        $router = new Router($container, []);
        $response = $router->__invoke(new ServerRequest, $next);
        $this->assertEquals('Hello world!', $response->getBody()->getContents());
    }
}
