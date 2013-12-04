<?php

namespace Sale\Controller;

use CatalogApplication;
use Sale\Model\SnippetModel;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class SnippetController implements ControllerProviderInterface
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
            $snippets = $app['model.snippet']->getAll();
            return $app->render('admin/snippet/index.twig', [
                'snippets' => $snippets
            ]);
        })->bind('adminSnippet.Index');

        $controllers->get('/add', function () use ($app) {
            return $app->render('admin/snippet/add.twig', [
            ]);
        })->bind('adminSnippet.Add');

        $controllers->post('/add', function (Request $request) use ($app) {
            $snippet = $request->get('snippet');
            if ($snippet['type'] == SnippetModel::TYPE_SINGLE) {
                $snippet_value = [
                    'name' => ['Да', 'Нет'],
                    'sysval' => [1, 0],
                ];
            } else {
                $snippet_value = $request->get('snippet_value');
            }

            $app['model.snippet']->insert($snippet, $snippet_value);
            return $app->redirect($app->url('adminSnippet.Index'));
        })->bind('adminSnippet.Create');

        $controllers->get('/edit/{id}', function ($id) use ($app) {
            $snippet = $app['model.snippet']->get($id);
            return $app->render('admin/snippet/add.twig', [
                'snippet' => $snippet,
            ]);
        })->bind('adminSnippet.Edit');

        $controllers->post('/edit/{id}', function (Request $request, $id) use ($app) {
            $snippet = $request->get('snippet');
            $snippet_value = $request->get('snippet_value');

            $app['model.snippet']->update($id, $snippet, $snippet_value);
            return $app->redirect($app->url('adminSnippet.Index'));
        })->bind('adminSnippet.Save');

        $controllers->get('/remove/{id}', function ($id) use ($app) {
            $app['model.snippet']->delete($id);
            return $app->redirect($app->url('adminSnippet.Index'));
        })->bind('adminSnippet.Remove');


        return $controllers;
    }
}