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

class SearchController implements ControllerProviderInterface
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

        $controllers->get('/compare', function(Request $request) use ($app){
            $ids = $request->get('ap');

            //Получим
            $appartments = $diff = [];
            $apartmentImages = $apartmentSnippets = $apartmentHouse = $houseSnippets = [];

            foreach($ids as $id){
                $appartments[] = $app['model.apartment']->get($id);
            }
            foreach($appartments as $apartment){
                $apartmentImages[$apartment['id']] = $app['model.file']->getForType(ApartmentModel::OBJECT_TYPE, $apartment['id']);
                $apartmentSnippets[$apartment['id']] = $app['model.apartment']->getSnippetsRows($apartment['id']);
                $houseSnippets[$apartment['id']] = $app['model.house']->getSnippetsRows($apartment['house_id']);
                $apartmentHouse[$apartment['id']] = $app['model.house']->get($apartment['house_id']);
            }

            for($i=0;$i<count($appartments)-1;$i++){
                //Основные
                $_diff = array_diff_assoc($appartments[$i], $appartments[$i+1]);

                foreach($_diff as $k=>$v){
                    $diff[$k] = true;
                }



                //
                $_id1 = $appartments[$i]['id'];
                $_id2 = $appartments[$i+1]['id'];

                $_diff = array_diff_assoc($apartmentHouse[$_id1], $apartmentHouse[$_id2]);

                foreach($_diff as $k=>$v){
                    $diff[$k] = true;
                }
                if(isset($apartmentSnippets[$_id1]) && $apartmentSnippets[$_id2]){
                    foreach($apartmentSnippets[$_id1] as $k=>$v){
                        $_v = implode('', $v);
                        $_expand1[$k] = $_v;
                    }
                    foreach($apartmentSnippets[$_id2] as $k=>$v){
                        $_v = implode('', $v);
                        $_expand2[$k] = $_v;
                    }
                    $_diff = array_diff_assoc($_expand1, $_expand2);

                    foreach($_diff as $k=>$v){
                        $diff[$k] = true;
                    }
                }

                if(isset($houseSnippets[$_id1]) && $houseSnippets[$_id2]){
                    foreach($houseSnippets[$_id1] as $k=>$v){
                        $_v = implode('', $v);
                        $_expand1[$k] = $_v;
                    }
                    foreach($houseSnippets[$_id2] as $k=>$v){
                        $_v = implode('', $v);
                        $_expand2[$k] = $_v;
                    }
                    $_diff = array_diff_assoc($_expand1, $_expand2);

                    foreach($_diff as $k=>$v){
                        $diff[$k] = true;
                    }
                }
            }

            //var_dump($diff);
            return $app->render('compare.twig',
            [
                'diff'=>$diff,

                'apartments'=>$appartments,
                'apartmentImages'=>$apartmentImages,
                'apartmentSnippets'=>$apartmentSnippets,
                'apartmentHouse'=>$apartmentHouse,
                'houseSnippets'=>$houseSnippets,

            ]);

        })->bind('search.Compare');

        $controllers->get('/', function (Request $request) use ($app) {

            $filters = $request->get('filter');
            $snippets = $request->get('snippet');
            if(!is_null($snippets)){
                $filters['withIds'] = $app['model.apartment']->getIdsBySnippets($snippets);
            }
            $apartments = $app['model.apartment']->search($filters);

            //Снимепеты
            $hSnippets = $app['model.snippet']->getForType(HouseModel::OBJECT_TYPE);
            $aSnippets = $app['model.snippet']->getForType(ApartmentModel::OBJECT_TYPE);
            $apartmentImages = $apartmentSnippets = $apartmentHouse = $houseSnippets = [];
            foreach($apartments as $apartment){
                $apartmentImages[$apartment['id']] = $app['model.file']->getForType(ApartmentModel::OBJECT_TYPE, $apartment['id']);
                $apartmentSnippets[$apartment['id']] = $app['model.apartment']->getSnippetsRows($apartment['id']);
                $houseSnippets[$apartment['id']] = $app['model.house']->getSnippetsRows($apartment['house_id']);
                $apartmentHouse[$apartment['id']] = $app['model.house']->get($apartment['house_id']);

            }
            return $app->render('search.twig', [
                'apartments'=>$apartments,
                'filters'   =>  $filters,
                'hSnippets'   =>  $hSnippets,
                'aSnippets'   =>  $aSnippets,
                'checked'   =>  $snippets,
                'apartmentImages'=>$apartmentImages,
                'apartmentSnippets'=>$apartmentSnippets,
                'apartmentHouse'=>$apartmentHouse,
                'houseSnippets'=>$houseSnippets,
            ]);
        })->bind('search.Index');



         return $controllers;
    }


}