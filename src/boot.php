<?php

//DEBUG
$app['debug'] = true;
$app['twig.options'] = ['1'];
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
    'twig.options'	=>	array(
        'cache' => __DIR__.'/../cache/twig',
        'strict_variables'   =>  false
    )
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_mysql',
        'dbname'   => 'belsm',
        'host'	   => 'localhost',
        'user'	   => 'root',
        'password' => 'bratka',
        'charset'  => 'utf8'
    ),

));

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\SecurityServiceProvider());
$app->register(new Silex\Provider\RememberMeServiceProvider());

$app['security.firewalls'] = array(
	    	'main' => array(
		        'pattern' => '^/',
		        'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
		        'logout' => array('logout_path' => '/logout'),
		        'anonymous' => true,
		        'users' => array(
		            'admin' => array('ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
		            'user' => array('ROLE_USER', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
		        ),
                'remember_me' => array(
                    'name'     => 'cuermb',
                    'key'      => 'Ksk@#dI7$nfj@Iwn$nVJjs',
                    'lifetime' => 31536000, // 365 days in seconds
                    'path'     => '/',
                    'domain'   => '', // Defaults to the current domain from $_SERVER
                    ),
	    	),
    	);

$app['security.role_hierarchy'] = array(
    'ROLE_ADMIN' => array('ROLE_USER'),
);

$app['security.access_rules'] = array(
    array('^/admin', 'ROLE_ADMIN'), // This url is available as anonymous user
);


//Register model
$app['model.house'] = new \Sale\Model\HouseModel($app['db']);
$app['model.apartment'] = new \Sale\Model\ApartmentModel($app['db']);