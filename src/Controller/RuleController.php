<?php

namespace Drupal\images_to_entities\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Utility\LinkGeneratorInterface;
use Drupal\images_to_entities\Repository\FieldCodeMatch;
use Drupal\images_to_entities\Repository\Image;
use Drupal\images_to_entities\Repository\ItemMatch;
use Drupal\images_to_entities\Repository\Rule;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RuleController extends ControllerBase {

    /**
     * Returns a list of rules.
     *
     * @return array
     *   A simple renderable array.
     */
    public function index() {
        $image_repo = new Image();
        $images = $image_repo->getAll();

        if(count($images)>0){
            return [
                '#theme' => 'ite_task_do',
            ];
        }else{
            $rule_repo = new Rule();

            return [
                '#theme' => 'ite_rules',
                '#list_items' => $rule_repo->getAll()
            ];
        }
    }

    public function delete($rid) {
        $rule_repo = new Rule();
        $rule_repo->delete($rid);

        $response = new RedirectResponse("/admin/ite/rules");
        $response->send();
        return;
    }

    public function config($rid) {
        $rule_repo = new Rule();
        $rule = $rule_repo->find($rid);

        $fc_match_repo = new FieldCodeMatch();
        $fc_match = $fc_match_repo->getAllByRuleId($rid);

        return [
            '#theme' => 'ite_rule_config',
            '#fc_match_items' => $fc_match,
            '#rule' => $rule
        ];
    }

    public function fcmDelete($rid, $fcm_id) {
        $fcm_repo = new FieldCodeMatch();
        $fcm_repo->delete($fcm_id);

        $response = new RedirectResponse("/admin/ite/rules/{$rid}/config");
        $response->send();
        return;
    }

    public function fcmConfig($rid, $fcm_id) {
        $rule_repo = new Rule();
        $rule = $rule_repo->find($rid);

        $fc_match_repo = new FieldCodeMatch();
        $fc_match = $fc_match_repo->find($fcm_id);

        $item_match_repo = new ItemMatch();
        $item_match = $item_match_repo->getAllByFCMId($fcm_id);

        return [
            '#theme' => 'ite_fcm_config',
            '#item_matches' => $item_match,
            '#rule' => $rule,
            '#fc_match' => $fc_match,
        ];
    }

    public function itemDelete($rid, $fcm_id, $iid){
        $item_repo = new ItemMatch();
        $item_repo->delete($iid);

        $response = new RedirectResponse("/admin/ite/rules/{$rid}/config/{$fcm_id}/config");
        $response->send();
        return;
    }

}
