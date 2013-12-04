<?php

namespace Sale\Controller;

use CatalogApplication;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class UploadController implements ControllerProviderInterface
{

    /**
     * @param \CatalogApplication $app
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->post('/', function () use ($app) {

        })->bind('upload.Do');


        return $controllers;
    }
}