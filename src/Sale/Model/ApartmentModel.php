<?php

namespace Sale\Model;


use Core\Model\AbstractModel;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;

class ApartmentModel extends AbstractModel
{
    const OBJECT_TYPE = 2;

    use SnippetTrait;
    use FileTrait;

    public function getTableSchema()
    {
        $schema = new Schema();

        $apartmentTable = $schema->createTable($this->getTable());
        $apartmentTable->addColumn('id', 'integer', array('autoincrement' => true));
        $apartmentTable->setPrimaryKey(array('id'));
        $apartmentTable->addColumn('house_id', 'integer');
        $apartmentTable->addForeignKeyConstraint($this->app['model.house']->getTable(), ['house_id'], ['id'], ['onDelete' => 'CASCADE']);
        $apartmentTable->addColumn('cnt_room', 'integer');
        $apartmentTable->addColumn('square', 'integer');
        $apartmentTable->addColumn('cost', 'integer');

        return $apartmentTable;
    }

    public function getWithHouseName($id = null){
        $qb = $this->db->createQueryBuilder();
        $qb->select('a.id as aid, a.*,  h.name as hname, h.address as adr')
            ->from($this->getTable(), 'a')
            ->innerJoin('a', $this->app['model.house']->getTable(),'h', 'h.id = a.house_id')
           ;
        if(!is_null($id)){
            $qb->where('h.id = :id')->setParameter('id', $id);
        }
        return $qb->execute()->fetchAll();
    }
    public function getTable()
    {
        return 'apartment';
    }


}