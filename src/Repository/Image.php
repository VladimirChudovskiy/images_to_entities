<?php

namespace Drupal\images_to_entities\Repository;

class Image extends BaseRepo {

    protected $_table_name = 'ite_images';


    public function insertBatch($items){
        $query = \Drupal::database()->insert('mytable')
            ->fields([
                'rule_id',
                'fid',
                'filename',
                'uri',
                'alt',
                'title',
            ]);
        foreach ($items as $record) {
            $query->values($record);
        }
        $query->execute();
    }


}