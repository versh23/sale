<?php

namespace Sale\Model;


use Core\Model\AbstractModel;
use Doctrine\DBAL\Schema\Schema;

class FileModel extends AbstractModel
{


    public function getTableSchema()
    {
        $schema = new Schema();

        $fileTable = $schema->createTable($this->getTable());
        $fileTable->addColumn('id', 'integer', array('autoincrement' => true));
        $fileTable->setPrimaryKey(array('id'));
        $fileTable->addColumn('path', 'string', array('length' => 32));
        $fileTable->addColumn('mime', 'string', array('length' => 32));
        $fileTable->addColumn('original_name', 'string', array('length' => 32));
        $fileTable->addColumn('title', 'string', array('length' => 32));
        $fileTable->addColumn('description', 'string', array('length' => 32));
        $fileTable->addColumn('extension', 'string', array('length' => 10));
        $fileTable->addColumn('temp', 'integer', array('length' => 1, 'default'=> 1));
        $fileTable->addColumn('object_type', 'integer', ['notnull'=>false]);
        $fileTable->addColumn('object_id', 'string', ['notnull'=>false]);

        return $fileTable;
    }

    public function getTable()
    {
        return 'file';
    }

    //@TODO
    public static function table(){
        return 'file';
    }

    public function getForType($type, $id){
        if(is_null($id)){
            return $this->db->fetchAll(
                'select * from ' . $this->getTable() . ' where object_type = :type',
                [
                    'type'=>$type,
                ]
            );
        }else{
            return $this->db->fetchAll(
                'select * from ' . $this->getTable() . ' where object_type = :type and object_id = :id',
                [
                    'type'=>$type,
                    'id'=>$id,
                ]
            );
        }

    }

    public function fullRemove($img){
        $this->app['service.upload']->remove($img);
        $this->delete($img['id']);
    }

    public function clear($id, $type){
        $images = $this->getForType($type, $id);
        foreach($images as $img){
            $this->fullRemove($img);
        }
    }


}