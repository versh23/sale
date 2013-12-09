<?php

namespace Sale\Controller;

use SaleApplication;
use Sale\Model\ApartmentModel;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class ApartmentController implements ControllerProviderInterface
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
            $apartments = $app['model.apartment']->getAll();
            return $app->render('admin/apartment/index.twig', [
                'apartments' => $apartments
            ]);
        })->bind('adminApartment.Index');

        $controllers->get('/add', function () use ($app) {
            $houses = $app['model.house']->getList();
            $snippets = $app['model.snippet']->getForType(ApartmentModel::OBJECT_TYPE);
            return $app->render('admin/apartment/add.twig', [
                'houses' => $houses,
                'snippets' => $snippets,
            ]);
        })->bind('adminApartment.Add');

        $controllers->post('/add', function (Request $request) use ($app) {
            $apartment = $request->get('apartment');
            $id = $app['model.apartment']->insert($apartment);;

            $snippets = $request->get('snippet');
            $app['model.apartment']->addSnippet($snippets, $id);

            return $app->redirect($app->url('adminApartment.Index'));
        })->bind('adminApartment.Create');

        $controllers->get('/edit/{id}', function ($id) use ($app) {
            $apartment = $app['model.apartment']->getWithSnippets($id);
            $houses = $app['model.house']->getList();
            $snippets = $app['model.snippet']->getForType(ApartmentModel::OBJECT_TYPE);
            $checkedSnippets = $app['model.snippet']->getChecked($apartment);
            return $app->render('admin/apartment/add.twig', [
                'apartment' => $apartment,
                'houses' => $houses,
                'snippets' => $snippets,
                'checked' => $checkedSnippets,
            ]);
        })->bind('adminApartment.Edit');

        $controllers->post('/edit/{id}', function (Request $request, $id) use ($app) {
            $apartment = $request->get('apartment');
            $snippets = $request->get('snippet');
            $app['model.apartment']->update($id, $apartment);
            $app['model.apartment']->updateSnippets($id, $snippets);
            return $app->redirect($app->url('adminApartment.Index'));
        })->bind('adminApartment.Save');

        $controllers->get('/remove/{id}', function ($id) use ($app) {
            $app['model.apartment']->delete($id);
            $app['model.snippet']->clear($id, ApartmentModel::OBJECT_TYPE);

            return $app->redirect($app->url('adminApartment.Index'));
        })->bind('adminApartment.Remove');


        return $controllers;
    }
}