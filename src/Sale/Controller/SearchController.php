<?php

namespace Sale\Controller;

use Sale\Model\HouseModel;
use Sale\Model\PageModel;
use SaleApplication;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class SearchController implements ControllerProviderInterface
{
    private $app;

    /**
     * @param \SaleApplication $app
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $this->app = $app;

        /**
         * @var \SaleApplication $controllers
         */
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function (Request $request) use ($app) {
            return $app->render('search.twig', [
            ]);
        })->bind('search.Index');



         return $controllers;
    }


}