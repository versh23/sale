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
        $app['twig.loader']->addLoader(new \Twig_Loader_Filesystem(SRCROOT . '/Core/Resources'));
        $app['twig.form.templates'] = ['bootstrap.twig'];
    }
}