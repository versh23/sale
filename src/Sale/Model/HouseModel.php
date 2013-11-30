<?php

namespace Sale\Model;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Sale\ModelInterface;

class HouseModel extends AbstractModel {

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


}