<?php

namespace Drupal\images_to_entities\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Utility\LinkGeneratorInterface;
use Drupal\images_to_entities\Repository\FieldCodeMatch;
use Drupal\images_to_entities\Repository\Image;
use Drupal\images_to_entities\Repository\ItemMatch;
use Drupal\images_to_entities\Repository\Rule;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;

class ImageController extends ControllerBase {

    public function getAll(){
        $image_repo = new Image();
        $array = $image_repo->getAll();

        return new JsonResponse($array);
    }

    public function imageToEntity($id){
        $image_repo = new Image();
        $item = $image_repo->find($id);

        $result = $this->doAllStaff($item);
        $image_repo->delete($id);

        return new JsonResponse($result);
    }

    protected function doAllStaff($item){
        $rule_repo = new Rule();
        $rule = $rule_repo->find($item['rule_id']);

        $fcm_repo = new FieldCodeMatch();
        $fcms = $fcm_repo->getAllByRuleId($rule['id']);

        $node_data['type'] = $rule['entity_bundle'];

        $item_match_repo = new ItemMatch();
        foreach ($fcms as $fcm){
            if($fcm->field_type == 'entity_reference'){
                $item_match = $item_match_repo->getAllByFCMId($fcm->id);
                $code_result = eval($fcm->code);
                foreach ($item_match as $im){
                    if($im->string_match == $code_result){
                        $node_data[$fcm->field_name]['target_id'] = $im->tid;
                    }
                }
            }elseif($fcm->field_type == 'image'){
                $node_data[$fcm->field_name] = [
                    'target_id' => eval($fcm->code),
                    'alt' => '',
                    'title' => ''
                ];
            }else{
                $node_data[$fcm->field_name] = eval($fcm->code);
            }
        }

        $node = Node::create($node_data);
        $node->save();

        return $node_data;
    }

}
