<?php

namespace Test\Middleware;

use Superpress\Middleware\HttpBasicAuthentication;
use Zend\Diactoros\Response\TextResponse;
use Zend\Diactoros\ServerRequest;

class HttpBasicAuthenticationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function prevents_unauthenticated_access()
    {
        $middleware = new HttpBasicAuthentication([]);
        $response = $middleware->__invoke(new ServerRequest, function () {
            throw new \Exception('Next middleware is called but should not be called');
        });
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Basic realm="Superpress"', $response->getHeaderLine('WWW-Authenticate'));
    }

    /**
     * @test
     */
    public function prevents_access_to_unknown_user()
    {
        $middleware = new HttpBasicAuthentication([
            'bob' => 'secretpassword',
        ]);

        $request = new ServerRequest;
        $request = $request->withAddedHeader('Authorization', 'Basic ' . base64_encode('jane:somepassword'));

        $response = $middleware->__invoke($request, function () {
            throw new \Exception('Next middleware is called but should not be called');
        });
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function requires_correct_password()
    {
        $middleware = new HttpBasicAuthentication([
            'bob' => 'secretpassword',
        ]);

        $request = new ServerRequest;
        $request = $request->withAddedHeader('Authorization', 'Basic ' . base64_encode('bob:wrongpassword'));

        $response = $middleware->__invoke($request, function () {
            throw new \Exception('Next middleware is called but should not be called');
        });
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function calls_next_middleware_when_authenticated()
    {
        $middleware = new HttpBasicAuthentication([
            'bob' => 'secretpassword',
        ]);

        $request = new ServerRequest;
        $request = $request->withAddedHeader('Authorization', 'Basic ' . base64_encode('bob:secretpassword'));

        $response = $middleware->__invoke($request, function () {
            return new TextResponse('Hello world!');
        });
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello world!', $response->getBody()->getContents());
    }
}
