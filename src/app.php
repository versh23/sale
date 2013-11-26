<?php

use Silex\Application;

class CatalogApplication extends Application
{
    use Application\TwigTrait;
	use Application\UrlGeneratorTrait;
    use Silex\Route\SecurityTrait;
    use Application\SecurityTrait;
}

$app = new CatalogApplication();

require_once __DIR__.'/../src/boot.php';

return $app;