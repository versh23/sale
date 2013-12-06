<?php

namespace Core\Service;


use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class ImageService
{
    /**
     * @var UploadService
     */
    private $uploadService = null;
    /**
     * @var Imagine
     */
    private $imagine = null;

    public function __construct(UploadService $uploadService, Imagine $imagine){
        $this->uploadService = $uploadService;
        $this->imagine = $imagine;
    }

    public function getThumb($image, $width = 100, $height = 100){

        //@TODO First check if exist
        $size    = new Box($width, $height);
        $mode    = ImageInterface::THUMBNAIL_INSET;
        $targetPath = $this->uploadService->getTargetDir($image['path'], UploadService::DIR_THUMB);
        $hashName =$this->uploadService->getHashName($image['path']);
        if(!file_exists($targetPath)){
            mkdir($targetPath, 0777);
        }
        $newName = $this->generateThumbName($hashName, $width, $height);
        $fullThumbName = $targetPath.$newName. '.' . $image['extension'];
        $fullOriginalName = $this->uploadService->getFullName($image);
        $this->imagine->open($fullOriginalName)
            ->thumbnail($size, $mode)
            ->save($fullThumbName);


        return $this->getThumbUrl($image, $newName);
    }

    private function generateThumbName($hashName, $width, $height)
    {
        return $hashName . '-' . $width . '_' . $height;
    }

    private function getThumbUrl($image, $thumbName)
    {
        $host = $_SERVER['HTTP_HOST'];
        $targetPath = $this->uploadService->getTargetDir($image['path'], UploadService::DIR_THUMB, false);
        return 'http://' . $host .$targetPath . $thumbName. '.' . $image['extension'];
    }

}