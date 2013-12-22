<?php

namespace Sale\Controller;

use SaleApplication;
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
     * @param \SaleApplication $app
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->post('/save', function(Request $request) use ($app){
            if($request->isXmlHttpRequest()){
                $title = $request->get('title');
                $desc = $request->get('desc');
                $id  = $request->get('id');

                if($id){
                    $app['model.file']->update($id, [
                        'title' =>  $title,
                        'description'   => $desc
                    ]);

                    return $app->json(['status'=>'success']);
                }
            }
        })->bind('upload.Save');

        $controllers->post('/', function (Request $request) use ($app) {

            $files = [];
            if($ck = $request->get('type', false)){
                $procFiles = $request->files->get('upload');
                $procFiles = [$procFiles];
            }else{
                $procFiles = $request->files->get('files');
            }

            /**
             * @var UploadedFile $file
             */
            foreach ($procFiles as $file) {
                /**
                 * @var UploadService $uploadService
                 */
                $uploadService = $app['service.upload'];
                $fileData = $uploadService->pickUp($file);
                /**
                 * @var ImageService $imageService
                 */
                $imageService = $app['service.image'];

                //@todo подумать над передачей размеров кастомных
                if($ck){
                    $url = $uploadService->getUrl($fileData);
                    $func = $request->get('CKEditorFuncNum');
                    $response = "
                    <script type=\"text/javascript\">window.parent.CKEDITOR.tools.callFunction($func, '$url', '');</script>
                    ";
                    return new Response(trim($response));
                }
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