<?php

namespace Sale\Controller;

use CatalogApplication;
use Sale\Model\HouseModel;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class HouseController implements ControllerProviderInterface
{

    /**
     * @param \CatalogApplication $app
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function () use ($app) {
            $houses = $app['model.house']->getAll();
            return $app->render('admin/house/index.twig', [
                'houses' => $houses
            ]);
        })->bind('adminHouse.Index');

        $controllers->get('/add', function () use ($app) {
            $snippets = $app['model.snippet']->getForType(HouseModel::OBJECT_TYPE);
            return $app->render('admin/house/add.twig', [
                'snippets' => $snippets
            ]);
        })->bind('adminHouse.Add');

        $controllers->post('/add', function (Request $request) use ($app) {
            $house = $request->get('house');
            $snippets = $request->get('snippet');
            $id = $app['model.house']->insert($house);
            $app['model.house']->addSnippet($snippets, $id);

            return $app->redirect($app->url('adminHouse.Index'));
        })->bind('adminHouse.Create');

        $controllers->get('/edit/{id}', function ($id) use ($app) {
            $house = $app['model.house']->getWithSnippets($id);
            $snippets = $app['model.snippet']->getForType(HouseModel::OBJECT_TYPE);
            $checkedSnippets = $app['model.snippet']->getChecked($house);
            return $app->render('admin/house/add.twig', [
                'house' => $house,
                'snippets' => $snippets,
                'checked' => $checkedSnippets
            ]);
        })->bind('adminHouse.Edit');

        $controllers->post('/edit/{id}', function (Request $request, $id) use ($app) {
            $house = $request->get('house');
            $snippets = $request->get('snippet');
            $app['model.house']->update($id, $house);
            $app['model.house']->updateSnippets($id, $snippets);
            return $app->redirect($app->url('adminHouse.Index'));
        })->bind('adminHouse.Save');

        $controllers->get('/remove/{id}', function ($id) use ($app) {
            $app['model.house']->delete($id);
            $app['model.snippet']->clear($id, HouseModel::OBJECT_TYPE);
            return $app->redirect($app->url('adminHouse.Index'));
        })->bind('adminHouse.Remove');

        return $controllers;
    }
}