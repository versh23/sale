<?php
namespace Sale\Model;

trait SnippetTrait
{

    public function getWithSnippets($id)
    {

        $object = $this->get($id);

        $object['snippets'] = $this->getSnippets($id);

        return $object;
    }

    private function getSnippets($id)
    {
        return $this->db->fetchAll('select * from snippet_value_match where object_id = :oid and object_type = :t', [
            'oid' => $id,
            't' => $this::OBJECT_TYPE
        ]);
    }

    public function updateSnippets($objectId, $newSnippets)
    {
        $oldSnippets = $this->getSnippets($objectId);
        $updates = [];
        $deletes = [];
        $insertes = [];

        $normalNewSnippets = [];
        //Преобразуем новые в массивы значений
        foreach ($newSnippets as $sysname => $dirtySysval) {
            $type = SnippetModel::TYPE_MULTI;
            if (!is_array($dirtySysval)) {
                $type = SnippetModel::TYPE_SINGLE;
                $dirtySysval = [$dirtySysval];
            }
            foreach ($dirtySysval as $ds) {
                list($sysval, $snippet_value_id) = $this->info($ds);
                $normalNewSnippets[$sysname]['values'][] = $sysval;
                $normalNewSnippets[$sysname]['map_valueId'][$sysval] = $snippet_value_id;
                $normalNewSnippets[$sysname]['type'] = $type;
            }
        }
        foreach ($oldSnippets as $osn) {

            if (!in_array($osn['sysval'], $normalNewSnippets[$osn['sysname']]['values'])) {

                //что то не так..
                //Это update ?
                if (isset($normalNewSnippets[$osn['sysname']])) {
                    if ($normalNewSnippets[$osn['sysname']]['type'] == SnippetModel::TYPE_SINGLE) {
                        $sysval = array_pop($normalNewSnippets[$osn['sysname']]['values']);
                        $updates[] = [
                            'data' => [
                                'sysval' => $sysval,
                            ],
                            'where' => [
                                'object_type' => $this::OBJECT_TYPE,
                                'object_id' => (int)$objectId,
                                'sysname' => $osn['sysname'],
                            ]
                        ];
                    } else {
                        $deletes[] = [
                            'object_type' => $this::OBJECT_TYPE,
                            'object_id' => (int)$objectId,
                            'snippet_value_id' => (int)$osn['snippet_value_id'],
                            'sysname' => $osn['sysname'],

                        ];
                    }

                } else {
                    //Достаточно этго, потому что его ваще нет у объекта
                    $deletes[] = [
                        'object_type' => $this::OBJECT_TYPE,
                        'object_id' => (int)$objectId,
                        'sysname' => $osn['sysname'],

                    ];
                }
            } else {
                //Наверное новый ебучий снипет мать его
                //А нет, все хуже
                //Нужно понять, это новый или нихуя

                foreach ($normalNewSnippets[$osn['sysname']]['values'] as $i => $v) {
                    if ($osn['sysval'] == $v) {
                        unset($normalNewSnippets[$osn['sysname']]['values'][$i]);
                    }
                }
            }

        }

        //Тут по идее остались только норвые
        foreach ($normalNewSnippets as $sysname => $data) {
            if(count($data['values'])){
                $insertValues = [];
                foreach ($data['values'] as $v) {
                    $insertValues[] = $v . '__' . $data['map_valueId'][$v];
                }

                $insertes[] = [
                    $sysname => $insertValues
                ];
            }
        }
        foreach($insertes as $ins){
            $this->addSnippet($ins, $objectId);
        }


        foreach ($updates as $up) {
            $this->db->update('snippet_value_match', $up['data'], $up['where']);
        }
        foreach ($deletes as $del) {
            $this->db->delete('snippet_value_match', $del);
        }

    }

    private function info($value)
    {
        preg_match('/(.*?)\__(\d+)/', $value, $matches);
        return [$matches[1], $matches[2]];
    }

    public function addSnippet($snippet, $id)
    {
        $object_id = $id;
        $object_type = $this::OBJECT_TYPE;
        $willAdd = [];
        foreach ($snippet as $sysname => $value) {
            if (!is_array($value)) $value = [$value];
            foreach ($value as $v) {
                list($sysval, $snippet_value_id) = $this->info($v);
                $willAdd[] = [
                    'object_id' => $object_id,
                    'object_type' => $object_type,
                    'snippet_value_id' => $snippet_value_id,
                    'sysval' => $sysval,
                    'sysname' => $sysname,
                ];
            }
        }

        foreach ($willAdd as $data) {
            $this->db->insert('snippet_value_match', $data);
        }
        return true;
    }

}