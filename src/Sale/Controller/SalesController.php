<?php

namespace Sale\Controller;

use SaleApplication;
use Silex\Application;
use Silex\ControllerProviderInterface;

class SalesController implements ControllerProviderInterface
{

    /**
     * @param \SaleApplication $app
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function () use ($app) {
            return $app->render('admin/sales/index.twig', [
            ]);
        })->bind('adminSales.Index');

        return $controllers;
    }
}