<?php
namespace Sale\Model;

trait SnippetTrait{

    public function getWithSnippets($id){

        $object = $this->get($id);

        $object['snippets'] = $this->db->fetchAll('select * from snippet_value_match where object_id = :oid and object_type = :t', [
            'oid'   =>  $id,
            't'     =>  $this::OBJECT_TYPE
        ]);

        return $object;
    }

    public function addSnippet($snippet, $id){
        $object_id = $id;
        $object_type = $this::OBJECT_TYPE;
        $willAdd = [];
        foreach($snippet as $sysname => $value){
            if(!is_array($value)) $value = [$value];
            foreach($value as $v){
                preg_match('/(.*?)\__(\d+)/', $v, $matches);
                $willAdd[] = [
                    'object_id'        =>  $object_id,
                    'object_type'      =>  $object_type,
                    'snippet_value_id' =>  $matches[2],
                    'sysval'           =>  $matches[1],
                    'sysname'          =>  $sysname,
                ];
            }
        }

        foreach($willAdd as $data){
            $this->db->insert('snippet_value_match', $data);
        }
        return true;
    }



}