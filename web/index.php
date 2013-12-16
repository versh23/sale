<?php

use Sale\Controller\ApartmentController;
use Sale\Controller\HouseController;
use Sale\Controller\MainController;
use Sale\Controller\PageController;
use Sale\Controller\SalesController;
use Sale\Controller\SnippetController;
use Sale\Controller\UploadController;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Validator\Constraints as Assert;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * @var SaleApplication $app ;
 */
$app = require __DIR__ . '/../src/app.php';

$app->before(function (Request $request) use ($app) {


    $route = $request->get('_route');

    if (preg_match('/admin(\w+)\./', $route, $matches)) {
        $app['twig']->addGlobal('mainMenu', 'adminIndex');
        $app['twig']->addGlobal('subMenu', (isset($matches[1])) ? $matches[1] : $route);
    } else {
        if($route === 'Main.Pages'){
            $params = $request->get('_route_params');
            $sysname = (is_null($params['sysname'])) ? 'main' : $params['sysname'];
            $app['twig']->addGlobal('subMenu', null);
            $app['twig']->addGlobal('mainMenu', $sysname);
        }else{
            $app['twig']->addGlobal('subMenu', 'adminIndex');
            $app['twig']->addGlobal('mainMenu', $route);
        }

    }

});



$app->mount('/admin/house', new HouseController());
$app->mount('/admin/apartment', new ApartmentController());
$app->mount('/admin/snippet', new SnippetController());
$app->mount('/admin/sales', new SalesController());
$app->mount('/admin/page', new PageController());
$app->mount('/upload', new UploadController());
$app->mount('/', new MainController());


$app->run();
