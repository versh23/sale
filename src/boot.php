<?php

define('WEBROOT', __DIR__ . '/../web');
define('SRCROOT', __DIR__ );

//DEBUG
use Core\Service\ImageService;
use Core\Service\UploadService;
use Silex\Provider\FormServiceProvider;

$app['debug'] = true;
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
    'twig.options' => array(
        'cache' => __DIR__ . '/../cache/twig',
        'strict_variables' => false
    )
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'dbname' => 'belsm',
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'bratka',
        'charset' => 'utf8'
    ),

));

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\SecurityServiceProvider());
$app->register(new Silex\Provider\RememberMeServiceProvider());
$app->register(new Core\Provider\ImagineServiceProvider());

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
            'name' => 'cuermb',
            'key' => 'Ksk@#dI7$nfj@Iwn$nVJjs',
            'lifetime' => 31536000, // 365 days in seconds
            'path' => '/',
            'domain' => '', // Defaults to the current domain from $_SERVER
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
//@TODO mb closure?
$app['model.house'] = new \Sale\Model\HouseModel($app);
$app['model.apartment'] = new \Sale\Model\ApartmentModel($app);
$app['model.snippet'] = new \Sale\Model\SnippetModel($app);
$app['model.file'] = new \Sale\Model\FileModel($app);

//Custom services
$app['service.upload'] = function($app){
    return new UploadService($app['model.file']);
};

$app['service.image'] = function($app){
    return new ImageService($app['service.upload'], $app['imagine']);
};

$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
    'locale' => 'ru',
));
$app->register(new FormServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {

    $twig->addExtension(new Core\Twig\TwigExtension($app));

    return $twig;
}));

$app->register(new \Core\Provider\BootstrapServiceProvider());
