<?php

namespace Superpress\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Superpress\Middleware;

class HttpBasicAuthentication implements Middleware
{
    /**
     * @var array Key: username, Value: password
     */
    private $users = [];

    public function __construct(array $users)
    {
        $this->users = $users;
    }

    /**
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $header = $request->getHeaderLine('Authorization');
        if (strpos($header, 'Basic') !== 0) {
            // No authentication found: 401
            // Hint: set the 'WWW-Authenticate' to 'Basic realm="Superpress"' to trigger authentication
            // in web browsers.
            // TODO
        }

        // Decode the username and password from the HTTP header
        $header = explode(':', base64_decode(substr($header, 6)), 2);
        $username = $header[0];
        $password = isset($header[1]) ? $header[1] : null;

        if (isset($this->users[$username]) && ($this->users[$username] === $password)) {
            // Authenticated
            // TODO
        }

        // Authentication failed: 403
        // TODO



        return $next($request);
    }
}
