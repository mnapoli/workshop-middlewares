<?php
declare(strict_types = 1);

namespace Test\Middleware;

use Psr\Http\Message\ResponseInterface;
use Superpress\Middleware\Pipe;
use Zend\Diactoros\Response\TextResponse;
use Zend\Diactoros\ServerRequest;

class PipeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function calls_middlewares_in_correct_order()
    {
        $pipe = new Pipe([
            function ($request, callable $next) {
                /** @var ResponseInterface $response */
                $response = $next($request);
                return $response->withAddedHeader('Cache-Control', 'no-cache');
            },
            function ($request, callable $next) {
                return new TextResponse('Hello world!');
            },
        ]);

        /** @var ResponseInterface $response */
        $response = $pipe(new ServerRequest, function () {});

        $this->assertEquals('Hello world!', $response->getBody()->getContents());
        $this->assertEquals(['no-cache'], $response->getHeader('Cache-Control'));
    }

    /**
     * @test
     */
    public function calls_next_middleware()
    {
        $pipe = new Pipe([
            function ($request, callable $next) {
                return $next($request);
            },
            function ($request, callable $next) {
                return $next($request);
            },
        ]);

        /** @var ResponseInterface $response */
        $response = $pipe(new ServerRequest, function () {
            return new TextResponse('Hello world!');
        });

        $this->assertEquals('Hello world!', $response->getBody());
    }
}
