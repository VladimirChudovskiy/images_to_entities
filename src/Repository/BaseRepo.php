<?php

namespace Drupal\images_to_entities\Repository;

class BaseRepo {

    protected $_table_name = '';

    public function getAll() {
        return \Drupal::database()->select($this->_table_name, "t")
            ->fields("t")
            ->execute()
            ->fetchAll();
    }

    public function insert($data) {
        return \Drupal::database()->insert($this->_table_name)
            ->fields($data)
            ->execute();
    }

    public function find($id){
        return \Drupal::database()->select($this->_table_name, "t")
            ->fields("t")
            ->condition('id', $id)
            ->execute()
            ->fetchAssoc();
    }

    public function update($id, $data){
        return \Drupal::database()->update($this->_table_name)
            ->fields($data)
            ->condition('id', $id)
            ->execute();
    }

    public function delete($id){
        return \Drupal::database()->delete($this->_table_name)
            ->condition('id', $id)
            ->execute();
    }

}