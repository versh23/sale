<?php

namespace Sale\Controller;

use SaleApplication;
use Sale\Model\ApartmentModel;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ApartmentController implements ControllerProviderInterface
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
            $apartments = $app['model.apartment']->getWithHouseName();
            return $app->render('admin/apartment/index.twig', [
                'apartments' => $apartments
            ]);
        })->bind('adminApartment.Index');

        $controllers->match('/add', function (Request $request) use ($app) {
            $snippets = $app['model.snippet']->getForType(ApartmentModel::OBJECT_TYPE);

            $form = $this->getForm();

            $form->handleRequest($request);

            if($form->isValid()){
                $apartment = $form->getData();
                $files = $request->get('files');
                $id = $app['model.apartment']->insert($apartment);;

                $snippets = $request->get('snippet');
                $app['model.apartment']->addSnippet($snippets, $id);
                $app['model.apartment']->addFiles($files, $id);

                return $app->redirect($app->url('adminApartment.Index'));
            }


            return $app->render('admin/apartment/add.twig', [
                'snippets' => $snippets,
                'form'  =>  $form->createView()
            ]);
        })->bind('adminApartment.Add');

        $controllers->match('/edit/{id}', function (Request $request, $id) use ($app) {
            $apartment = $app['model.apartment']->getWithSnippets($id);
            $snippets = $app['model.snippet']->getForType(ApartmentModel::OBJECT_TYPE);
            $checkedSnippets = $app['model.snippet']->getChecked($apartment);
            $images = $app['model.file']->getForType(ApartmentModel::OBJECT_TYPE, $id);

            $form = $this->getForm($apartment);

            $form->handleRequest($request);
            if($form->isValid()){
                $apartment = $form->getData();
                $files = $request->get('files');
                unset($apartment['snippets']);
                $snippets = $request->get('snippet');
                $app['model.apartment']->update($id, $apartment);
                $app['model.apartment']->updateSnippets($id, $snippets);
                $app['model.apartment']->updateFiles($id, $files, $images);
                return $app->redirect($app->url('adminApartment.Index'));
            }

            return $app->render('admin/apartment/add.twig', [
                'apartment' => $apartment,
                'snippets' => $snippets,
                'checked' => $checkedSnippets,
                'images' => $images,
                'form'  =>  $form->createView()
            ]);
        })->bind('adminApartment.Edit');


        $controllers->get('/remove/{id}', function ($id) use ($app) {
            $app['model.apartment']->delete($id);
            $app['model.snippet']->clear($id, ApartmentModel::OBJECT_TYPE);

            return $app->redirect($app->url('adminApartment.Index'));
        })->bind('adminApartment.Remove');


        return $controllers;
    }


    /**
     * @param null $data
     * @return Form
     */
    private function getForm($data = null){

        $houses = $this->app['model.house']->getList();
        $normal_houses = [];
        foreach($houses as $h){
            $normal_houses[$h['id']] = $h['name'];
        }

        $form = $this->app->form($data)
            ->add('house_id', 'choice', [
                'label'=>'В доме',
                'choices'   => $normal_houses
            ])
            ->add('cnt_room', 'integer',[
                'label'=>'Кол - во комнат',
                'constraints'   =>  [
                    new Assert\NotBlank(),
                    new Assert\Type('int')
                ]
            ])
            ->add('square', 'integer',[
                'label'=>'Квадратура',
                'constraints'   =>  [
                    new Assert\NotBlank(),
                    new Assert\Type('int')
                ]
            ])
            ->add('cost', 'integer',[
                'label'=>'Цена',
                'constraints'   =>  [
                    new Assert\NotBlank(),
                    new Assert\Type('int')
                ]
            ])
            ->add('custom_text', 'textarea',[
                'label'=>'Контент',
                'constraints'   =>  [
                    new Assert\NotBlank()
                ]
            ])
            ->add('save', 'submit', ['label'=>(is_null($data)) ? 'Добавить' : 'Сохранить'])

            ->getForm();

        return $form;
    }
}