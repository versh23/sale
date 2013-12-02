<?php

namespace Sale\Model;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use Sale\ModelInterface;

class SnippetModel extends AbstractModel {

    const TYPE_SINGLE = 1, TYPE_MULTI = 2;

    const TO_HOUSE = 1, TO_APARTMENT = 2;

    public function getTableSchema()
    {
        $schema = new Schema();

        $snippetTable = $schema->createTable('snippet');
        $snippetTable->addColumn('id', 'integer', array('autoincrement' => true));
        $snippetTable->setPrimaryKey(array('id'));
        $snippetTable->addColumn('label', 'string', array('length' => 32));
        $snippetTable->addColumn('type', 'integer');
        $snippetTable->addColumn('val', 'array');
        $snippetTable->addColumn('to_object', 'integer');

        return $snippetTable;
    }

    public function getTable()
    {
        return 'snippet';
    }

    public function get($id){
        $res = $this->db->fetchAssoc('SELECT * FROM ' . $this->getTable() . ' where id = :id', ['id'=>$id]);
        if($res){
            $res['val'] = $this->db->convertToPHPValue($res['val'], TYPE::TARRAY);
        }
        return $res;
    }

    public function update($id, $data){
        return $this->db->update($this->getTable(), $data, ['id'=>$id], [4 => Type::TARRAY]);
    }

    public function insert($data){
        return $this->db->insert($this->getTable(), $data, [4 => Type::TARRAY]);
    }


}