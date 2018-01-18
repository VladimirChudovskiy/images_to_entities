<?php

namespace Drupal\images_to_entities\Repository;

class ItemMatch extends BaseRepo {

    protected $_table_name = 'ite_item_matches';

    public function getAllByFCMId($fcm_id){
        return \Drupal::database()->select($this->_table_name, "t")
            ->fields("t")
            ->condition('field_code_match_id', $fcm_id)
            ->execute()
            ->fetchAll();
    }

    public function getAllByRuleId($rid){
        $fcm_repo = new FieldCodeMatch();
        $ids = [];

        foreach ($fcm_repo->getAllByRuleId($rid) as $k=>$obj){
            $ids[] = $obj->id;
        }

        return \Drupal::database()->select($this->_table_name, "t")
            ->fields("t")
            ->condition('field_code_match_id', $ids, 'IN')
            ->execute()
            ->fetchAll();
    }

}