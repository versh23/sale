<?php

namespace Sale\Controller;

use SaleApplication;
use Sale\Model\HouseModel;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;


class HouseController implements ControllerProviderInterface
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
            $houses = $app['model.house']->getAll();
            return $app->render('admin/house/index.twig', [
                'houses' => $houses
            ]);
        })->bind('adminHouse.Index');

        $controllers->match('/add', function (Request $request) use ($app) {
            $snippets = $app['model.snippet']->getForType(HouseModel::OBJECT_TYPE);
            /**
             * @var Form $form;
             */
            $form = $this->getForm();
            $form->handleRequest($request);

            if($form->isValid()){

                $files = $request->get('files');
                $house = $form->getData();
                $snippets = $request->get('snippet');
                $id = $app['model.house']->insert($house);
                $app['model.house']->addSnippet($snippets, $id);
                $app['model.house']->addFiles($files, $id);
                return $app->redirect($app->url('adminHouse.Index'));
            }

            return $app->render('admin/house/add.twig', [
                'form' => $form->createView(),
                'snippets' => $snippets
            ]);
        })->bind('adminHouse.Add');

        $controllers->match('/edit/{id}', function (Request $request, $id) use ($app) {
            $house = $app['model.house']->getWithSnippets($id);
            $snippets = $app['model.snippet']->getForType(HouseModel::OBJECT_TYPE);
            $images = $app['model.file']->getForType(HouseModel::OBJECT_TYPE, $id);
            $checkedSnippets = $app['model.snippet']->getChecked($house);
            /**
             * @var Form $form;
             */
            $form = $this->getForm($house);

            $form->handleRequest($request);
            if($form->isValid()){
                $house = $form->getData();
                unset($house['snippets']);
                $snippets = $request->get('snippet');
                $files = $request->get('files');

                $app['model.house']->update($id, $house);
                $app['model.house']->updateSnippets($id, $snippets);
                $app['model.house']->updateFiles($id, $files, $images);
                return $app->redirect($app->url('adminHouse.Index'));
            }

            return $app->render('admin/house/add.twig', [
                'house' => $house,
                'snippets' => $snippets,
                'checked' => $checkedSnippets,
                'images' => $images,
                'form' => $form->createView(),
            ]);
        })->bind('adminHouse.Edit');


        $controllers->get('/remove/{id}', function ($id) use ($app) {
            $app['model.house']->delete($id);
            $app['model.snippet']->clear($id, HouseModel::OBJECT_TYPE);
            $app['model.file']->clear($id, HouseModel::OBJECT_TYPE);
            return $app->redirect($app->url('adminHouse.Index'));
        })->bind('adminHouse.Remove');

        return $controllers;
    }

    private function getForm($data = null){
        $form = $this->app->form($data)
            ->add('name', 'text', [
                'label'=>'Название дома',
                'constraints'   =>  [
                    new Assert\NotBlank()
                ]
            ])
            ->add('address', 'text',[
                'label'=>'Адрес',
                'constraints'   =>  [
                    new Assert\NotBlank()
                ]
            ])
            ->add('material', 'choice', [
                    'choices' => HouseModel::getMaterials(),
                    'expanded' => false,
                    'label' => 'Тип материала',

                ]
            )
            ->add('floor', 'integer',[
                'label'=>'Этаж',
                'constraints'   =>  [
                    new Assert\NotBlank(),
                    new Assert\Type('int')
                ]
            ])
            ->add('count_apartments', 'integer',[
                'label'=>'Кол-во квартир',
                'constraints'   =>  [
                    new Assert\NotBlank(),
                    new Assert\Type('int')
                ]
            ])
            ->add('count_1', 'integer',[
                'label'=>'На этаже однокомнтаных',
                'constraints'   =>  [
                    new Assert\NotBlank(),
                    new Assert\Type('int')
                ],
            ])
            ->add('count_2', 'integer',[
                'label'=>'На этаже двухкомнтаных',
                'constraints'   =>  [
                    new Assert\NotBlank(),
                    new Assert\Type('int')
                ]
            ])
            ->add('count_3', 'integer',[
                'label'=>'На этаже трехкомнтаных',
                'constraints'   =>  [
                    new Assert\NotBlank(),
                    new Assert\Type('int')
                ]
            ])
            ->add('deliverydate', 'text',[
                'label'=>'Дата сдачи',
                'constraints'   =>  [
                    new Assert\NotBlank()
                ]
            ])
            ->add('save', 'submit', ['label'=>(is_null($data)) ? 'Добавить' : 'Сохранить'])

            ->getForm();

        return $form;
    }
}