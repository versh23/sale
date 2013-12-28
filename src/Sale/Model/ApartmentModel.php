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
        $apartmentTable->addColumn('custom_text', 'text');

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

    public function search($filters){

        $qb = $this->db->createQueryBuilder();
        $qb->select('a.id as aid, a.*,  h.name as hname, h.address as adr')
            ->from($this->getTable(), 'a')
            ->innerJoin('a', $this->app['model.house']->getTable(),'h', 'h.id = a.house_id')
        ;
        if(isset($filters['cost']) && $filters['cost'] != ''){
            $cost = $filters['cost'];
            if(isset($cost['from']) && $cost['from'] != ''){
                $qb->andWhere('a.cost >= :from')->setParameter('from', $cost['from']);
            }
            if(isset($cost['to']) && $cost['to'] != ''){
                $qb->andWhere('a.cost <= :to')->setParameter('to', $cost['to']);
            }
        }
        if(isset($filters['cnt_room']) && $filters['cnt_room'] != ''){
            $cnt_room = $filters['cnt_room'];
            $qb->andWhere('a.cnt_room = :cnt_room', $cnt_room)->setParameter('cnt_room', $cnt_room);
        }
        if(isset($filters['floor']) && $filters['floor'] != ''){
            $floor = $filters['floor'];
            $qb->andWhere('h.floor = :floor', $floor)->setParameter('floor', $floor);
        }
        if(isset($filters['material']) && $filters['material'] != '' && $filters['material'] != 0){
            $material = $filters['material'];
            $qb->andWhere('h.material = :material', $material)->setParameter('material', $material);
        }

        if(isset($filters['withIds']) && $filters['withIds'] != '' && is_array($filters['withIds'])){
            $ids = $filters['withIds'];
            if(!count($ids)) return [];
            $qb->andWhere('a.id in (' . implode(', ', $ids) . ')');
        }


        return $qb->execute()->fetchAll();

    }

    public function getIdsBySnippets($snippets){
        $aids = $apIds = $hIds = [];
        $qb = $this->db->createQueryBuilder();
        $qb->select('svm.*')
            ->from('snippet_value_match', 'svm')
            ;
        if(isset($snippets['house']) && is_array($snippets['house'])){
            $hSnippet = $snippets['house'];
            $sysvalCount = 0;
            foreach($hSnippet as $sysname=>$sysval){
                if(is_array($sysval)){
                    $imploder = '';
                    foreach($sysval as $sv){
                        $imploder .= "'$sv',";
                        $sysvalCount++;
                    }
                    $qb->andWhere("(svm.sysname = '$sysname' AND svm.sysval in (" . substr($imploder, 0, strlen($imploder) - 1) . "))");
                }else{
                    $sysvalCount++;
                    $qb->andWhere("(svm.sysname = '$sysname' AND svm.sysval = '$sysval')");

                }
            }
            $qb->andWhere('svm.object_type = 1');

            $res = $qb->execute()->fetchAll();
            $ids = [];
            foreach($res as $row){
                $ids[$row['object_id']]['sysvals'][] = $row['sysval'];
            }
            $normalIds = [];
            foreach($ids as $k=>$v){
                if(count($v['sysvals']) == $sysvalCount){
                    $normalIds[] = $k;
                }
            }
            //Это ид домов. найдем ид комнат
            if(count($normalIds)){
               $res = $this->db->fetchAll('SELECT id FROM ' . $this->getTable() . ' where house_id in (' . implode(', ', $normalIds) . ')');
               if(count($res)){
                   foreach($res as $row){
                       $hIds[$row['id']]= $row['id'];
                   }
               }
            }
            $qb->resetQueryPart('where');
        }

        if(isset($snippets['ap']) && is_array($snippets['ap'])){
            $hSnippet = $snippets['ap'];
            $sysvalCount = 0;
            foreach($hSnippet as $sysname=>$sysval){
                if(is_array($sysval)){
                    $imploder = '';
                    foreach($sysval as $sv){
                        $imploder .= "'$sv',";
                        $sysvalCount++;
                    }
                    $qb->andWhere("(svm.sysname = '$sysname' AND svm.sysval in (" . substr($imploder, 0, strlen($imploder) - 1) . "))");
                }else{
                    $sysvalCount++;
                    $qb->andWhere("(svm.sysname = '$sysname' AND svm.sysval = '$sysval')");

                }
            }
            $qb->andWhere('svm.object_type = 2');
            $res = $qb->execute()->fetchAll();

            $ids = [];
            if(count($res)){
                foreach($res as $row){
                    $ids[$row['object_id']]['sysvals'][] = $row['sysval'];
                    //$apIds[$row['object_id']]= $row[''];
                }
                foreach($ids as $k=>$v){
                    if(count($v['sysvals']) == $sysvalCount){
                        $apIds[$k]= $k;
                    }
                }
            }
        }
        if(count($hIds)){
            $aids = array_intersect($apIds, $hIds);
        }else{
            $aids = $apIds;
        }

        return $aids;

    }
}