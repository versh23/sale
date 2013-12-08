<?php

namespace Sale\Controller;

use CatalogApplication;
use Core\Service\ImageService;
use Core\Service\UploadService;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UploadController implements ControllerProviderInterface
{

    /**
     * @param \CatalogApplication $app
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];


        $controllers->post('/', function (Request $request) use ($app) {

            $files = [];
            /**
             * @var UploadedFile $file
             */
            foreach ($request->files->get('files') as $file) {
                /**
                 * @var UploadService $uploadService
                 */
                $uploadService = $app['service.upload'];
                $fileData = $uploadService->pickUp($file);
                /**
                 * @var ImageService $imageService
                 */
                $imageService = $app['service.image'];
                $url = $imageService->getThumb($fileData);
                $files[] = [
                    'url' => $url,
                    'id'  => $fileData['id']
                ];
            }

            return $app->json(['files'=>$files]);

        })->bind('upload.Do');


        return $controllers;
    }
}