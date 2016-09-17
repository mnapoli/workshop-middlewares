# Middleware Workshop

## Installation

You will need PHP 5.5 or greater. Clone the repository and run:

```
composer install
```

To run the web application you don't need to install and configure Apache or Nginx, you can use PHP's built-in webserver. Simply run `composer web` and visit [http://localhost:8000](http://localhost:8000/).

## Step 1: write and run a middleware

Write your first middleware. The web application (in [index.php](web/index.php)) should show "Hello world!" at [http://localhost:8000](http://localhost:8000/).

## Step 2: use the request

Use the request so that when querying [http://localhost:8000/?name=Bob](http://localhost:8000/?name=Bob) the application displays "Hello Bob!".

## Step 3: compose middlewares to handle errors nicely

Use a middleware [`Pipe`](src/Middleware/Pipe.php) to assemble multiple middlewares into a bigger application.

The [`ErrorHandler`](src/Middleware/ErrorHandler.php) middleware must run before the next middlewares: it will catch exceptions thrown in next middlewares and show an error page.

## Step 4: split the flow with a router

Use the router to map URLs to handlers (aka controllers).

## Step 5: authentication middleware

Write a middleware that requires a valid HTTP "Basic" authentication to access the website. To do that you can complete the existing [`HttpBasicAuthentication`](src/Middleware/HttpBasicAuthentication.php) class, you can also run the tests (test-driven development) with `composer tests`.

Once that is done, use the middleware in your application to prevent access to the whole website.
