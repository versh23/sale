<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

require_once __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../src/app.php';

$app->before(function (Request $request) use($app) {
	$route = $request->get('_route');

	if(preg_match('/admin(\w+)\./', $route, $matches)){
		$app['twig']->addGlobal('mainMenu', 'adminIndex');
		$app['twig']->addGlobal('subMenu', (isset($matches[1])) ? $matches[1] : $route);
	}else{
		$app['twig']->addGlobal('subMenu', 'adminIndex');
		$app['twig']->addGlobal('mainMenu', $route);
	}
	
});

$app->get('/', function() use($app){

	return $app->render('index.twig',[]);
})->bind('index');

//Главная админки
$app->get('/admin', function() use($app){
	return $app->render('admin/index.twig',[]);
})->bind('adminIndex');

$app->get('/admin/house', function() use($app){
	return $app->render('admin/house/index.twig',[]);
})->bind('adminHouse.Index');

$app->get('/admin/house/add', function() use($app){
	return $app->render('admin/house/add.twig',[]);
})->bind('adminHouse.Add');

$app->get('/admin/apartment/add', function() use($app){
	return $app->render('admin/apartment/add.twig',[]);
})->bind('adminApartment.Add');

$app->get('/admin/apartment', function() use($app){
	return $app->render('admin/apartment/index.twig',[]);
})->bind('adminApartment.Index');

$app->get('/login', function(Request $request) use($app){
	return $app->render('login.twig', [
	        'error'         => $app['security.last_error']($request),
	        'last_username' => $app['session']->get('_security.last_username'),
		]);
})->bind('loginPage');


$app->run();
