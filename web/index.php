<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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



/**
 * Houses Actions
 */
$app->get('/admin/house', function() use($app){

    $houses = $app['db']->fetchAll('SELECT * FROM house');

	return $app->render('admin/house/index.twig',[
        'houses'    =>  $houses
    ]);
})->bind('adminHouse.Index');

$app->get('/admin/house/add', function() use($app){
	return $app->render('admin/house/add.twig',[]);
})->bind('adminHouse.Add');

$app->get('admin/house/edit/{id}', function($id) use($app){
    $house = $app['db']->fetchAssoc('SELECT * FROM house where id = :id', ['id'=>$id]);
    return $app->render('admin/house/add.twig',[
        'house' =>  $house
    ]);
})->bind('adminHouse.Edit');

$app->get('admin/house/remove/{id}', function($id) use($app){
    $db =  $app['db'];
    $db->delete('house', ['id' => $id]);
    return $app->redirect($app->url('adminHouse.Index'));
})->bind('adminHouse.Remove');


$app->post('admin/house/edit/{id}', function(Request $request, $id) use($app){
    $house = $request->get('house');
    $db =  $app['db'];
    $db->update('house', $house, ['id'=>$id]);
    return $app->redirect($app->url('adminHouse.Index'));
})->bind('adminHouse.Save');

$app->post('/admin/house/add', function(Request $request) use($app){
	$house = $request->get('house');
	$db =  $app['db'];
	$db->insert('house', $house);
	return $app->redirect($app->url('adminHouse.Index'));
})->bind('adminHouse.Create');
/**
 * End Houses Actions
 */


/**
 * Apartment Actions
 */
$app->get('/admin/apartment/add', function() use($app){
    $houses = $app['db']->fetchAll('SELECT h.id as id, h.name as name FROM house as h');

	return $app->render('admin/apartment/add.twig',[
        'houses'    =>  $houses
    ]);
})->bind('adminApartment.Add');

$app->get('/admin/apartment', function() use($app){
    $apartments = $app['db']->fetchAll('SELECT * FROM apartment');
	return $app->render('admin/apartment/index.twig',[
        'apartments'    =>  $apartments
    ]);
})->bind('adminApartment.Index');

$app->get('admin/apartment/edit/{id}', function($id) use($app){
    $apartment = $app['db']->fetchAssoc('SELECT * FROM apartment where id = :id', ['id'=>$id]);
    $houses = $app['db']->fetchAll('SELECT h.id as id, h.name as name FROM house as h');
    return $app->render('admin/apartment/add.twig',[
        'apartment' =>  $apartment,
        'houses'    =>  $houses
    ]);
})->bind('adminApartment.Edit');

$app->get('admin/apartment/remove/{id}', function($id) use($app){
    $db =  $app['db'];
    $db->delete('apartment', ['id' => $id]);
    return $app->redirect($app->url('adminApartment.Index'));
})->bind('adminApartment.Remove');


$app->post('admin/apartment/edit/{id}', function(Request $request, $id) use($app){
    $apartment = $request->get('apartment');
    $db =  $app['db'];
    $db->update('apartment', $apartment, ['id'=>$id]);
    return $app->redirect($app->url('adminApartment.Index'));
})->bind('adminApartment.Save');

$app->post('/admin/apartment/add', function(Request $request) use($app){
    $apartment = $request->get('apartment');
    $db =  $app['db'];
    $db->insert('apartment', $apartment);
    return $app->redirect($app->url('adminApartment.Index'));
})->bind('adminApartment.Create');

/**
 * End Apartment Actions
 */

$app->get('/login', function(Request $request) use($app){
	return $app->render('login.twig', [
	        'error'         => $app['security.last_error']($request),
	        'last_username' => $app['session']->get('_security.last_username'),
		]);
})->bind('loginPage');


$app->run();
