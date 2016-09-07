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
