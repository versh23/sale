<?php

namespace Sale\Model;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use Sale\ModelInterface;

class SnippetModel extends AbstractModel {

//      SELECT object_id, h.*
//      FROM `snippet_value_match`
//      join house as h on h.id = object_id
//      WHERE `sysname` = 'structure' AND `sysval` IN ('sadik')


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
        $snippetTable->addColumn('sysname', 'string', array('length' => 20));
        $snippetTable->addColumn('to_object', 'integer');

        $snippetValueTable = $schema->createTable('snippet_value');
        $snippetValueTable->addColumn('id', 'integer', array('autoincrement' => true));
        $snippetValueTable->setPrimaryKey(array('id'));
        $snippetValueTable->addColumn('snippet_id', 'integer');
        $snippetValueTable->addForeignKeyConstraint('snippet', ['snippet_id'], ['id'], ['onDelete'=>'CASCADE']);
        $snippetValueTable->addColumn('name', 'string', array('length' => 32));
        $snippetValueTable->addColumn('sysval', 'string', array('length' => 16));

        $snippetValueMatchTable = $schema->createTable('snippet_value_match');
        $snippetValueMatchTable->addColumn('id', 'integer', array('autoincrement' => true));
        $snippetValueMatchTable->setPrimaryKey(array('id'));
        $snippetValueMatchTable->addColumn('object_id', 'integer');
        $snippetValueMatchTable->addColumn('object_type', 'integer');
        $snippetValueMatchTable->addColumn('snippet_value_id', 'integer');
        $snippetValueMatchTable->addForeignKeyConstraint('snippet_value', ['snippet_value_id'], ['id'], ['onDelete'=>'CASCADE']);
        //Денормализация
        $snippetValueMatchTable->addColumn('sysval', 'string', array('length' => 16));
        $snippetValueMatchTable->addColumn('sysname', 'string', array('length' => 20));

        return [$snippetTable, $snippetValueTable, $snippetValueMatchTable];

    }

    public function getTable()
    {
        return 'snippet';
    }

    public function getForType($id){
        return $this->db->fetchAll('SELECT * FROM ' . $this->getTable() . ' where to_object = :to_object', ['to_object'=>$id]);

    }



}