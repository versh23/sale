<?php
namespace Core\Provider;

use Core\Twig\TwitterBootstrapExtension;
use Silex\Application;
use Silex\ServiceProviderInterface;


class BootstrapServiceProvider implements ServiceProviderInterface
{
    public function boot(Application $app)
    {
        $app['twig']->addExtension(new TwitterBootstrapExtension());
    }

    public function register(Application $app)
    {
        $app['twig.loader.filesystem']->addPath(SRCROOT . '/Core/Resources', 'Sale');
        $app['twig.form.templates'] = ['@Sale\bootstrap.twig'];
    }
}