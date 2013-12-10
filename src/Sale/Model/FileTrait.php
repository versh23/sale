<?php

namespace Sale\Model;


trait FileTrait {

    public function addFiles($files, $id){
        $type = $this::OBJECT_TYPE;

        foreach($files as $file){
            $updateData = [
                'object_type'=>$type,
                'object_id'=>$id,
                'temp'  =>  0
            ];
            $this->app['model.file']->update($file, $updateData);
        }
    }

    public function updateFiles($id, $new, $old){
        foreach($old as $oldItem){
            $key = array_search($oldItem['id'], $new);
            if(false === $key){
                // Старый пропал - удалили
                $this->app['model.file']->fullRemove($oldItem);
            }else{
                unset($new[$key]);
            }
        }
        if(count($new)){
            $this->addFiles($new, $id);
        }
    }
} 