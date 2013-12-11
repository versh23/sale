<?php

use Sale\Controller\ApartmentController;
use Sale\Controller\HouseController;
use Sale\Controller\SalesController;
use Sale\Controller\SnippetController;
use Sale\Controller\UploadController;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Validator\Constraints as Assert;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * @var CatalogApplication $app ;
 */
$app = require __DIR__ . '/../src/app.php';

$app->before(function (Request $request) use ($app) {


    $route = $request->get('_route');

    if (preg_match('/admin(\w+)\./', $route, $matches)) {
        $app['twig']->addGlobal('mainMenu', 'adminIndex');
        $app['twig']->addGlobal('subMenu', (isset($matches[1])) ? $matches[1] : $route);
    } else {
        $app['twig']->addGlobal('subMenu', 'adminIndex');
        $app['twig']->addGlobal('mainMenu', $route);
    }

});


$app->get('/', function () use ($app) {
    return $app->render('index.twig', []);
})->bind('index');

$app->get('/login', function (Request $request) use ($app) {
    return $app->render('login.twig', [
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ]);
})->bind('loginPage');

//Главная админки
$app->get('/admin', function () use ($app) {
    return $app->render('admin/index.twig', []);
})->bind('adminIndex');

$app->mount('/admin/house', new HouseController());
$app->mount('/admin/apartment', new ApartmentController());
$app->mount('/admin/snippet', new SnippetController());
$app->mount('/admin/sales', new SalesController());
$app->mount('/upload', new UploadController());


$app->run();
