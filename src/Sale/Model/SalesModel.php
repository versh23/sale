<?php

namespace Sale\Model;


use Core\Model\AbstractModel;
use Doctrine\DBAL\Schema\Schema;

class SalesModel extends AbstractModel
{


    public function getTableSchema()
    {
        $schema = new Schema();

        $salesTable = $schema->createTable($this->getTable());
        $salesTable->addColumn('id', 'integer', array('autoincrement' => true));
        $salesTable->setPrimaryKey(array('id'));
        $salesTable->addColumn('apartment_id', 'integer');
        $salesTable->addForeignKeyConstraint($this->app['model.apartment']->getTable(), ['apartment_id'], ['id'], ['onDelete' => 'CASCADE']);

        $salesTable->addColumn('ap_number', 'integer');
        $salesTable->addColumn('fio', 'string');

        return $salesTable;
    }

    public function getTable()
    {
        return 'sales';
    }


}