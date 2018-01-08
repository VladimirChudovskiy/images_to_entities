<?php

namespace Drupal\images_to_entities\Repository;

class Rule {

    public function getAll() {
        $data = [];

        //Fake data
        $data = [
            ['id' => 1,'name'=>'Nashi raboty1','entity_type'=>'node','entity_bundle'=>'article'],
            ['id' => 2,'name'=>'Nashi raboty2','entity_type'=>'node','entity_bundle'=>'article'],
            ['id' => 3,'name'=>'Nashi raboty3','entity_type'=>'node','entity_bundle'=>'article'],
            ['id' => 4,'name'=>'Nashi raboty4','entity_type'=>'node','entity_bundle'=>'article'],
        ];

        return $data;
    }

}