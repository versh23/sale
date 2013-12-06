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
        $fileTable->addColumn('extension', 'string', array('length' => 10));
        $fileTable->addColumn('temp', 'integer', array('length' => 1, 'default'=> 1));

        return $fileTable;
    }

    public function getTable()
    {
        return 'file';
    }


}