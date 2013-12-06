<?php
namespace Core\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;


class ImagineServiceProvider implements ServiceProviderInterface
{
    public function boot(Application $app)
    {
    }

    public function register(Application $app)
    {
        if(!isset($app['imagine.factory'])) {
            $app['imagine.factory'] = 'Gd';
        }

        $app['imagine'] = $app->share(function ($app) {
            $class = sprintf('\Imagine\%s\Imagine', $app['imagine.factory']);
            return new $class();
        });
    }
}