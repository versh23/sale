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
        $salesTable->addColumn('ap_pod', 'integer');
        $salesTable->addColumn('ap_floor', 'integer');
        $salesTable->addColumn('fio', 'string');
        $salesTable->addColumn('address', 'string');
        $salesTable->addColumn('passport', 'string');
        $salesTable->addColumn('phone', 'string');
        $salesTable->addColumn('dogovor', 'string');

        $salesTable->addColumn('create_time', 'integer');
        $salesTable->addColumn('birth_place', 'string');
        $salesTable->addColumn('birth_day', 'string');

        return $salesTable;
    }

    public function getTable()
    {
        return 'sales';
    }

    public function getAll(){
        return $this->db->fetchAll('SELECT s.*, ap.cnt_room, h.name, h.address FROM ' . $this->getTable() . ' as s inner join apartment as ap on ap.id = s.apartment_id inner join house as h on h.id = ap.house_id');
    }

    public function getStats($apid){
        $now = new \DateTime();
        $year = $now->format('Y');
        $month = $now->format('m');
        $nowt = $now->setDate($year, $month, 1)->format('U');

        $res =  $this->db->fetchAll('select count(s.id) as cnt_all, (select count(ss.id) from ' . $this->getTable() . ' as ss where ss.apartment_id = :apid and ss.create_time >= :ctime) as cnt_month from ' . $this->getTable() . ' as s where s.apartment_id = :apid', [
                'apid'  =>  $apid,
                'ctime' =>  $nowt
            ]);

        return $res[0];

    }

    public function getForHouse($id, $pod){
        $qb = $this->db->createQueryBuilder();

        $qb->select('*')
            ->from($this->getTable(), 's')
            ->innerJoin('s', $this->app['model.apartment']->getTable(), 'ap', 'ap.id=s.apartment_id')
            ->innerJoin('ap', $this->app['model.house']->getTable(), 'h', 'h.id = ap.house_id')
            ->where('h.id = :id')->setParameter('id', $id)
            ->andWhere('s.ap_pod = :pod')->setParameter('pod', $pod);

        return $qb->execute()->fetchAll();
    }
}