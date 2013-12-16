<?php

namespace Sale\Controller;

use SaleApplication;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class PageController implements ControllerProviderInterface
{
    private $app;

    /**
     * @param \SaleApplication $app
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $this->app = $app;

        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function () use ($app) {
            $pages = $app['model.page']->getAll();
            return $app->render('admin/page/index.twig', [
                'pages' => $pages
            ]);
        })->bind('adminPage.Index');

        $controllers->match('/add', function (Request $request) use ($app) {

            $form = $this->getForm();

            $form->handleRequest($request);

            if($form->isValid()){
                $page = $form->getData();
                $id = $app['model.page']->insert($page);;

                return $app->redirect($app->url('adminPage.Index'));
            }


            return $app->render('admin/page/add.twig', [
                'form'  =>  $form->createView()
            ]);
        })->bind('adminPage.Add');

        $controllers->match('/edit/{id}', function (Request $request, $id) use ($app) {
            $page = $app['model.page']->get($id);

            $form = $this->getForm($page);

            $form->handleRequest($request);
            if($form->isValid()){
                $page = $form->getData();
                $app['model.page']->update($id, $page);
                return $app->redirect($app->url('adminPage.Index'));
            }

            return $app->render('admin/page/add.twig', [
                'page' => $page,
                'form'  =>  $form->createView()
            ]);
        })->bind('adminPage.Edit');


        $controllers->get('/remove/{id}', function ($id) use ($app) {
            $app['model.page']->delete($id);

            return $app->redirect($app->url('adminPage.Index'));
        })->bind('adminPage.Remove');


        return $controllers;
    }


    /**
     * @param null $data
     * @return Form
     */
    private function getForm($data = null){

        $form = $this->app->form($data)
            ->add('sysname', 'text',[
                'label'=>'Системное имя',
                'constraints'   =>  [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('title', 'text',[
                'label'=>'Заголовок',
                'constraints'   =>  [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('content', 'textarea',[
                'label'=>'Контент',
                'constraints'   =>  [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('save', 'submit', ['label'=>(is_null($data)) ? 'Добавить' : 'Сохранить'])

            ->getForm();

        return $form;
    }
}