<?php

namespace Core\Service;

use Sale\Model\FileModel;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadService
{

    /**
     * @var null|\Sale\Model\FileModel
     */
    private $model = null;

    const DIR_THUMB = 'thumbs', DIR_UPLOAD = 'uploads';

    public function __construct(FileModel $model){
        $this->model = $model;
    }

    public $allowedExtension = [
        'jpg', 'jpeg', 'png'
    ];

    public function getThumbDir($absolute = true)
    {
        return (!$absolute) ? '/'. self::DIR_THUMB .'/' : WEBROOT . '/'. self::DIR_THUMB .'/';
    }

    public function getUploadDir($absolute = true)
    {
        return (!$absolute) ? '/'. self::DIR_UPLOAD .'/' : WEBROOT . '/'. self::DIR_UPLOAD .'/';
    }

    /**
     * Do insert and move to uploads dir
     * @param UploadedFile $file
     */
    public function pickUp(UploadedFile $file){
        //@todo Image only?
        $fileExtension = $file->guessClientExtension();
        if(!in_array($fileExtension, $this->allowedExtension)){
            //@TODO return invalid extension
            throw(new \Exception("invalid extension"));
        }else{
            $path = $this->generatePath($file);
            $originalFileName = $file->getClientOriginalName();
            if(strlen($originalFileName) > 31){
                $originalFileName = substr($originalFileName, 0, 31);
            }
            $fileData = [
                'path'  =>  $path,
                'mime'  =>  $file->getMimeType(),
                'extension'  =>  $fileExtension,
                'original_name'  =>  $originalFileName,
            ];
            $id = $this->model->insert($fileData);
            $dir = $this->getTargetDir($path, self::DIR_UPLOAD);
            $hashName = $this->getHashName($path);
            $file->move($dir, $hashName . '.' . $fileExtension);
            $fileData['id'] = $id;

            return $fileData;
        }
    }

    private function generatePath(UploadedFile $file)
    {
        $uploadDir = $this->getUploadDir();
        $fileName = $file->getFilename();
        $ext = $file->guessClientExtension();
        $iterate = 0;
        do {
            $iterate++;
            if ($iterate > 30) {
                throw new \Exception("Too many iterate in UploadsService:generateName");
            }
            //@TODO think about hash
            $hash = hash("sha256", $fileName . time());
            $dirs = /*substr($hash, 0, 2).'/'.*/
                substr($hash, 2, 2) . '/';
            $hashName = substr($hash, 4, 15);
            $newFileName = $uploadDir . $dirs . $hashName;
        } while (file_exists($newFileName . '.' . $ext));

        return $dirs . $hashName;
    }

    public function getUrl($data, $absolute = true)
    {
        $host = $_SERVER['HTTP_HOST'];
        $prefix = ($absolute) ? 'http://' . $host : '';
        $uploadDir = $this->getUploadDir(false);
        return $prefix . $uploadDir . $data['path'] . '.' . $data['extension'];
    }

    public function getTargetDir($path, $rootDir, $absolute = true)
    {
        $hashDir = substr($path, 0 , 2) . '/';
        if($rootDir == self::DIR_UPLOAD){
            return $this->getUploadDir($absolute) . $hashDir;
        }else{
            return $this->getThumbDir($absolute) . $hashDir;
        }
    }

    public  function getHashName($path)
    {
        return substr($path, 3);
    }

    public function getFullName($file){
        return $this->getUploadDir().$file['path'] . '.' . $file['extension'];
    }

    //@TODO clear cache too or not
    public function remove($file){
        return unlink($this->getFullName($file));
    }

} 