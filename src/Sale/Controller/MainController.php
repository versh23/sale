<?php

namespace Sale\Controller;

use Sale\Model\ApartmentModel;
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

            $images = $app['model.file']->getForType(PageModel::OBJECT_TYPE, 'main');

            //Дома
            $houses = $app['model.house']->getAll();
            $houseImages = $houseSnippets = [];
            foreach($houses as $house){
                $houseImages[$house['id']] = $app['model.file']->getForType(HouseModel::OBJECT_TYPE, $house['id']);
                $houseSnippets[$house['id']] = $app['model.house']->getSnippetsRows($house['id']);
            }

            return $app->render('index.twig', [
                'images'=>$images,
                'houses'=>$houses,
                'houseImages'=>$houseImages,
                'houseSnippets'=>$houseSnippets,
            ]);
        })
            ->bind('main');

        $controllers->get('/house/{id}', function($id) use($app) {

            $images = $app['model.file']->getForType(HouseModel::OBJECT_TYPE, $id);

            //Квартиры
            $apartments = $app['model.apartment']->getWithHouseName($id);
            $house = $app['model.house']->get($id);

            $apartmentImages = $apartmentSnippets = [];
            foreach($apartments as $apartment){
                $apartmentImages[$apartment['id']] = $app['model.file']->getForType(ApartmentModel::OBJECT_TYPE, $apartment['id']);
                $apartmentSnippets[$apartment['id']] = $app['model.apartment']->getSnippetsRows($apartment['id']);

            }
            return $app->render('house.twig', [
                'images'=>$images,
                'apartments'=>$apartments,
                'apartmentImages'=>$apartmentImages,
                'apartmentSnippets'         => $apartmentSnippets,
                'house'         => $house
            ]);
        })
            ->bind('house.show');
        $controllers->get('/apartment/{id}', function($id) use($app) {


            $images = $app['model.file']->getForType(ApartmentModel::OBJECT_TYPE, $id);

            $apartment = $app['model.apartment']->get($id);
            $house = $app['model.house']->get($apartment['house_id']);
            $apartmentSnippets = [];
            $apartmentSnippets[$apartment['id']] = $app['model.apartment']->getSnippetsRows($apartment['id']);


            return $app->render('apartment.twig', [
                'images'=>$images,
                'apartment' =>  $apartment,
                'house'=>$house,
                'apartmentSnippets'         => $apartmentSnippets,

            ]);
        })
            ->bind('apartment.show');
        $controllers->get('/about', function() use($app) {

            return $app->render('about.twig', [
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