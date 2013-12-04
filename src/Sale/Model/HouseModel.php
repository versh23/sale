<?php

namespace Sale\Model;


use Doctrine\DBAL\Schema\Schema;
use Sale\ModelInterface;

class HouseModel extends AbstractModel
{

    use SnippetTrait;

    const MATERIAL_PANEL = 1, MATERIAL_BRICK = 2, TYPE_MONOLITH = 3;
    const OBJECT_TYPE = 1;

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

    public function getList()
    {
        return $this->db->fetchAll('SELECT h.id as id, h.name as name FROM ' . $this->getTable() . ' as h');
    }


}