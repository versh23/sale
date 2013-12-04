<?php

namespace Sale\Model;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Sale\ModelInterface;

class HouseModel extends AbstractModel {

    const MATERIAL_PANEL = 1, MATERIAL_BRICK = 2,TYPE_MONOLITH = 3;

    public function getTableSchema()
    {
        $schema = new Schema();

        $houseTable = $schema->createTable($this->getTable());
        $houseTable->addColumn('id', 'integer', array('autoincrement' => true));
        $houseTable->setPrimaryKey(array('id'));
        $houseTable->addColumn('name', 'string', array('length' => 32));
        $houseTable->addColumn('address', 'string', array('length' => 32));
        $houseTable->addColumn('material', 'integer', array('length' => 1));
        $houseTable->addColumn('floor', 'integer', array('length' => 2));
        $houseTable->addColumn('deliverydate', 'string', array('length' => 32));

        return $houseTable;
    }

    public function getTable()
    {
        return 'house';
    }

    public function getList(){
        return $this->db->fetchAll('SELECT h.id as id, h.name as name FROM ' . $this->getTable() . ' as h');
    }

    public function addSnippet($snippet, $id){
        /*
         *object_id, object_type, snippet_value_id, sysval, sysname
         */
        $object_id = $id;
        $object_type = SnippetModel::TO_HOUSE;
        $willAdd = [];
        foreach($snippet as $sysname => $value){
            if(!is_array($value)) $value = [$value];
            foreach($value as $v){
                preg_match('/(.*?)\__(\d+)/', $v, $matches);
                $willAdd[] = [
                    'object_id'        =>  $object_id,
                    'object_type'      =>  $object_type,
                    'snippet_value_id' =>  $matches[2],
                    'sysval'           =>  $matches[1],
                    'sysname'          =>  $sysname,
                ];
            }
        }
        foreach($willAdd as $data){
            $this->db->insert('snippet_value_match', $data);
        }
        return true;
    }

}