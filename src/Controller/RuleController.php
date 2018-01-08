<?php

namespace Drupal\images_to_entities\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\images_to_entities\Repository\Rule;

class RuleController extends ControllerBase {

    /**
     * Returns a list of rules.
     *
     * @return array
     *   A simple renderable array.
     */
    public function index() {

        $rule_repo = new Rule();

        return [
            '#theme' => 'ite_rules',
            '#list_items' => $rule_repo->getAll()
        ];

    }

}
