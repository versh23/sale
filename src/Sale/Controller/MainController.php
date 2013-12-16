<?php

namespace Sale\Controller;

use SaleApplication;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class MainController implements ControllerProviderInterface
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

        $controllers->get('/login', function (Request $request) use ($app) {
            return $app->render('login.twig', [
                'error' => $app['security.last_error']($request),
                'last_username' => $app['session']->get('_security.last_username'),
            ]);
        })->bind('loginPage');

        //Главная админки
        $controllers->get('/admin', function () use ($app) {
            return $app->render('admin/index.twig', []);
        })->bind('adminIndex');

        $controllers->get('/{sysname}', function ($sysname) use ($app) {

            if(is_null($sysname)) $sysname = 'main';

            //try

            $page = $app['model.page']->getBySysname($sysname);

            return $app->render('index.twig', [
                'page'=>$page
            ]);
        })
            ->bind('Main.Pages')
            ->value('sysname', null)
        ;



         return $controllers;
    }


}