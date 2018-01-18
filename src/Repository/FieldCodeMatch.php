<?php

namespace Drupal\images_to_entities\Repository;

class FieldCodeMatch extends BaseRepo {

    protected $_table_name = 'ite_field_code_matches';

    public function getAllByRuleId($rid){
        return \Drupal::database()->select($this->_table_name, "t")
            ->fields("t")
            ->condition('rule_id', $rid)
            ->execute()
            ->fetchAll();
    }

}