<?php

namespace Sale\Model;


use Core\Model\AbstractModel;
use Doctrine\DBAL\Schema\Schema;

class HouseModel extends AbstractModel
{

    use SnippetTrait;
    use FileTrait;

    const MATERIAL_PANEL = 1, MATERIAL_BRICK = 2, MATERIAL_MONOLITH = 3;
    const OBJECT_TYPE = 1;

    public static function getMaterials(){
        return [
            self::MATERIAL_PANEL => 'Панельный',
            self::MATERIAL_BRICK => 'Кирпичный',
            self::MATERIAL_MONOLITH => 'Монолитный',
        ];
    }

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
        $houseTable->addColumn('count_apartments', 'integer');

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