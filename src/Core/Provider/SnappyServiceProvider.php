<?php
namespace Core\Provider;

use Knp\Snappy\Pdf;
use Silex\Application;
use Silex\ServiceProviderInterface;


class SnappyServiceProvider implements ServiceProviderInterface
{
    public function boot(Application $app)
    {
    }

    public function register(Application $app)
    {

        $app['snappy.pdf'] = $app->share(function ($app) {
            return new Pdf(
                isset($app['snappy.pdf_binary']) ? $app['snappy.pdf_binary'] : '/usr/bin/wkhtmltopdf.sh',
                isset($app['snappy.pdf_options']) ? $app['snappy.pdf_options'] : [
                    'encoding'  =>  'UTF-8'
                ]
            );
        });
    }
}