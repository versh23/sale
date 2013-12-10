<?php

use Silex\Application;

class SaleApplication extends Application
{
    use Application\TwigTrait;
    use Application\UrlGeneratorTrait;
    use Silex\Route\SecurityTrait;
    use Application\SecurityTrait;
    use Silex\Application\FormTrait;
}

$app = new SaleApplication();

require_once __DIR__ . '/../src/boot.php';

return $app;