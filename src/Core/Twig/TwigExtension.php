<?php

namespace Core\Twig;


class TwigExtension extends \Twig_Extension{

    private $app;

    public function __construct(\SaleApplication $app){
        $this->app = $app;
    }
    public function getName()
    {
        return 'core.extension';
    }


    public function getFunctions()
    {
        return [
          new \Twig_SimpleFunction('getThumb', [$this->app['service.image'], 'getThumb'])
        ];
    }
}