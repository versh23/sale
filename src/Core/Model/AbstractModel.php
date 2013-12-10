<?php

namespace Core\Model;


use Doctrine\DBAL\Connection;
use Silex\Application;

abstract class AbstractModel
{

    abstract protected function getTable();

    abstract protected function getTableSchema();

    /**
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->db = $app['db'];
    }

    public function getAll()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->getTable());
    }

    public function get($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTable() . ' where id = :id', ['id' => $id]);
    }

    public function delete($id)
    {
        return $this->db->delete($this->getTable(), ['id' => $id]);
    }

    public function update($id, $data)
    {
        return $this->db->update($this->getTable(), $data, ['id' => $id]);
    }

    public function insert($data)
    {
        $this->db->insert($this->getTable(), $data);

        return $this->db->lastInsertId();
    }
}