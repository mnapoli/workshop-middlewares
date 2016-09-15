<?php

namespace Superpress;

use Superpress\Blog\ArticleRepository;
use Twig_Environment;
use Twig_Loader_Filesystem;

class Container
{
    /**
     * @return Twig_Environment
     */
    public function twig()
    {
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/Views');
        return new Twig_Environment($loader, [
            'debug' => true,
            'cache' => false,
            'strict_variables' => false,
        ]);
    }

    /**
     * @return ArticleRepository
     */
    public function articleRepository()
    {
        return new ArticleRepository();
    }
}
