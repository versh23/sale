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
            $this->db->update(FileModel::table(), $updateData, ['id'=>$file]);
        }
    }
} 