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
            $apartmentImages = $apartmentSnippets = $apartmentHouse =[];
            foreach($apartments as $apartment){
                $apartmentImages[$apartment['id']] = $app['model.file']->getForType(ApartmentModel::OBJECT_TYPE, $apartment['id']);
                $apartmentSnippets[$apartment['id']] = $app['model.apartment']->getSnippetsRows($apartment['id']);
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
            ]);
        })->bind('search.Index');



         return $controllers;
    }


}