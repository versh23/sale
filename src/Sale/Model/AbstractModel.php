<?php

namespace Sale\Model;


use Doctrine\DBAL\Connection;

abstract class AbstractModel {

    abstract protected function getTable();
    abstract protected function getTableSchema();

    /**
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;

    public function __construct(Connection $db){
        $this->db = $db;
    }

    public function getAll(){
        return $this->db->fetchAll('SELECT * FROM ' . $this->getTable());
    }

    public function get($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTable() . ' where id = :id', ['id'=>$id]);
    }

    public function delete($id)
    {
        return $this->db->delete($this->getTable(), ['id' => $id]);
    }

    public function update($id, $data)
    {
        return $this->db->update($this->getTable(), $data, ['id'=>$id]);
    }

    public function insert($data)
    {
        return $this->db->insert($this->getTable(), $data);
    }
}