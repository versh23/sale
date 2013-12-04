<?php

namespace Sale\Model;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Sale\ModelInterface;

class ApartmentModel extends AbstractModel
{
    const OBJECT_TYPE = 2;

    use SnippetTrait;

    public function getTableSchema()
    {
        $schema = new Schema();

        $apartmentTable = $schema->createTable($this->getTable());
        $apartmentTable->addColumn('id', 'integer', array('autoincrement' => true));
        $apartmentTable->setPrimaryKey(array('id'));
        $apartmentTable->addColumn('house_id', 'integer');
        //@TODO получить название таблицы другим способом?
        $apartmentTable->addForeignKeyConstraint('house', ['house_id'], ['id'], ['onDelete' => 'CASCADE']);
        $apartmentTable->addColumn('cnt_room', 'integer');
        $apartmentTable->addColumn('square', 'integer');

        return $apartmentTable;
    }

    public function getTable()
    {
        return 'apartment';
    }


}