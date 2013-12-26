<?php

namespace Sale\Model;


use Core\Model\AbstractModel;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;

class SettingsModel extends AbstractModel
{

    public function getTableSchema()
    {
        $schema = new Schema();

        $settingsTable = $schema->createTable($this->getTable());
        $settingsTable->addColumn('id', 'integer', array('autoincrement' => true));
        $settingsTable->setPrimaryKey(array('id'));
        $settingsTable->addColumn('sitename', 'string');
        $settingsTable->addColumn('description', 'text');
        $settingsTable->addColumn('keywords', 'text');
        $settingsTable->addColumn('address', 'string');
        $settingsTable->addColumn('latlon', 'string');
        $settingsTable->addColumn('custom_text', 'text');

        return $settingsTable;
    }


    public function getTable()
    {
        return 'settings';
    }


}