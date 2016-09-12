<?php
declare(strict_types = 1);

namespace Test\Middleware;

use Superpress\Middleware\ErrorHandler;
use Zend\Diactoros\ServerRequest;

class ErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function sets_http_status_to_500()
    {
        $middleware = new ErrorHandler;
        $response = $middleware->__invoke(new ServerRequest, function () {
            throw new \Exception('Hello world');
        });
        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shows_error_page()
    {
        $middleware = new ErrorHandler;
        $response = $middleware->__invoke(new ServerRequest, function () {
            throw new \Exception('Hello world');
        });
        $this->assertContains('Error', (string) $response->getBody());
        $this->assertContains('Hello world', (string) $response->getBody());
    }
}
