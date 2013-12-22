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

        $controllers->get('/', function() use($app) {

            $page = $app['model.settings']->getAll();
            $page = (count($page)) ? array_pop($page) : null;
            $id = null;
            if(!is_null($page)){
                $id = $page['id'];
            }
            $images = $app['model.file']->getForType(PageModel::OBJECT_TYPE, 'main');


            //Дома
            $houses = $app['model.house']->getAll();
            $houseImages = [];
            foreach($houses as $house){
                $houseImages[$house['id']] = $app['model.file']->getForType(HouseModel::OBJECT_TYPE, $house['id']);
            }

            return $app->render('index.twig', [
                'page'=>$page,
                'images'=>$images,
                'houses'=>$houses,
                'houseImages'=>$houseImages,
            ]);
        })
            ->bind('main');

        $controllers->get('/about', function() use($app) {
            $page = $app['model.settings']->getAll();
            $page = (count($page)) ? array_pop($page) : null;
            $id = null;
            if(!is_null($page)){
                $id = $page['id'];
            }
            return $app->render('about.twig', [
                'page'  =>  $page
            ]);
        })
            ->bind('about');

        $controllers->get('/page/{sysname}', function ($sysname) use ($app) {

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