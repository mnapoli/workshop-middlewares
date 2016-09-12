<?php
declare(strict_types = 1);

namespace Superpress\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Superpress\Middleware;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Zend\Diactoros\Response\HtmlResponse;

class ErrorHandler implements Middleware
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        try {
            return $next($request);
        } catch (\Exception $e) {
            $whoops = $this->createWhoops();
            $output = $whoops->handleException($e);
            return new HtmlResponse($output, 500);
        }
    }

    /**
     * @return Run
     */
    private function createWhoops()
    {
        $whoops = new Run();
        $whoops->writeToOutput(false);
        $whoops->allowQuit(false);
        $handler = new PrettyPageHandler;
        $handler->handleUnconditionally(true);
        $whoops->pushHandler($handler);
        return $whoops;
    }
}
