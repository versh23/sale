<?php

namespace Sale\Model;


use Core\Model\AbstractModel;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;

class PageModel extends AbstractModel
{

    public function getTableSchema()
    {
        $schema = new Schema();

        $pageTable = $schema->createTable($this->getTable());
        $pageTable->addColumn('id', 'integer', array('autoincrement' => true));
        $pageTable->setPrimaryKey(array('id'));
        $pageTable->addColumn('sysname', 'string');
        $pageTable->addColumn('title', 'string');
        $pageTable->addColumn('content', 'text', ['notnull'=>false]);

        return $pageTable;
    }

    public function getBySysname($sysname){
        $qb = $this->db->createQueryBuilder();

        $qb->select('p.*')->from($this->getTable(), 'p')
            ->where('p.sysname = :sysname')->setParameter('sysname', $sysname);

        return $qb->execute()->fetch();
    }

    public function getTable()
    {
        return 'page';
    }


}