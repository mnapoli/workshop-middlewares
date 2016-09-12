<?php

namespace Superpress;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface Middleware
{
    /**
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next);
}
