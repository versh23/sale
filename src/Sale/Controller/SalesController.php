<?php

namespace Sale\Controller;

use SaleApplication;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;


class SalesController implements ControllerProviderInterface
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
            $sales = $app['model.sales']->getAll();
            return $app->render('admin/sales/index.twig', [
                'sales'=>$sales
            ]);
        })->bind('adminSales.Index');

        $controllers->match('/add', function (Request $request) use ($app) {

            $form = $this->getForm();
            $form->handleRequest($request);

            if($form->isValid()){

                $sale = $form->getData();
                $id = $app['model.sales']->insert($sale);
                return $app->redirect($app->url('adminSales.Index'));
            }

            return $app->render('admin/sales/add.twig', [
                'form' => $form->createView(),
            ]);
        })->bind('adminSales.Add');

        $controllers->match('/edit/{id}', function (Request $request, $id) use ($app) {
            $sale = $app['model.sales']->get($id);
            /**
             * @var Form $form;
             */
            $form = $this->getForm($sale);

            $form->handleRequest($request);
            if($form->isValid()){
                $sale = $form->getData();

                $app['model.sales']->update($id, $sale);
                return $app->redirect($app->url('adminSales.Index'));
            }

            return $app->render('admin/sales/add.twig', [
                'form' => $form->createView(),
            ]);
        })->bind('adminSales.Edit');


        $controllers->get('/remove/{id}', function ($id) use ($app) {
            $app['model.sales']->delete($id);
            return $app->redirect($app->url('adminSales.Index'));
        })->bind('adminSales.Remove');

        $controllers->get('/stats', function (Request $request) use ($app) {

            $houses = $app['model.house']->getList();
            $cHouse = $request->get('house', null);
            $cnt_room = $floor = $roomPerFloor = $csales = $saleinfo = null;

            if(!is_null($cHouse)){
                $_house = $app['model.house']->get($cHouse);
                $cnt_room = $_house['count_apartments'];
                $floor = $_house['floor'];
                $roomPerFloor = $_house['count_1'] + $_house['count_2'] + $_house['count_3'];
                $_csales = $app['model.sales']->getForHouse($cHouse);

                foreach($_csales as $row){
                    $csales[] = $row['ap_number'];
                    $saleinfo[$row['ap_number']] = $row;
                }

            }
            return $app->renderView('admin/sales/stats.twig',
            [
                'houses'    =>  $houses,
                'cHouse'    =>  $cHouse,
                'cnt_room'  =>  $cnt_room,
                'floor'     =>  $floor,
                'roomPerFloor'=>$roomPerFloor,
                'csales'    => $csales,
                'saleinfo'  => $saleinfo
            ]);
        })->bind('adminSales.Stats');



        return $controllers;
    }

    private function getForm($data = null){

        $list = $this->app['model.apartment']->getWithHouseName();
        $normalList =[];
        foreach($list as $row){
            $normalList[$row['aid']] = $row['hname'] . ', ' . $row['adr'] . ' - ' . $row['cnt_room'] . ' комнат(ы)';
        }
        $form = $this->app->form($data)
            ->add('ap_number', 'integer', [
                'label'=>'Номер квартиры',
                'constraints'   =>  [
                    new Assert\NotBlank()
                ]
            ])
            ->add('ap_pod', 'integer', [
                'label'=>'Номер подъезда',
                'constraints'   =>  [
                    new Assert\NotBlank()
                ]
            ])
            ->add('ap_floor', 'integer', [
                'label'=>'Этаж',
                'constraints'   =>  [
                    new Assert\NotBlank()
                ]
            ])
            ->add('dogovor', 'text', [
                'label'=>'Договор',
                'constraints'   =>  [
                    new Assert\NotBlank()
                ]
            ])
            ->add('apartment_id', 'choice', [
                'label'=>'Тип квартиры',
                'choices'   => $normalList
            ])
            ->add('fio', 'text', [
                'label'=>'ФИО покупателя',
                'constraints'   =>  [
                    new Assert\NotBlank()
                ]
            ])
            ->add('passport', 'text', [
                'label'=>'Паспортные данные',
                'constraints'   =>  [
                    new Assert\NotBlank()
                ]
            ])
            ->add('phone', 'text', [
                'label'=>'Телефон',
                'constraints'   =>  [
                    new Assert\NotBlank()
                ]
            ])
            ->add('address', 'text', [
                'label'=>'Адрес проживание',
                'constraints'   =>  [
                    new Assert\NotBlank()
                ]
            ])

            ->add('save', 'submit', ['label'=>(is_null($data)) ? 'Добавить' : 'Сохранить'])

            ->getForm();

        return $form;
    }

}