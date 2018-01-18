<?php

namespace Drupal\images_to_entities\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\images_to_entities\Repository\FieldCodeMatch;
use Drupal\images_to_entities\Repository\ItemMatch;
use Drupal\images_to_entities\Repository\Rule;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ItemCreate extends FormBase {


    public function buildForm(array $form, FormStateInterface $form_state, AccountInterface $user = NULL, $rid = NULL, $fcm_id = NULL) {
        $rule_repo = new Rule();
        $rule = $rule_repo->find($rid);

        $fcm_repo = new FieldCodeMatch();
        $fcm = $fcm_repo->find($fcm_id);

        $form['rule_id'] = [
            '#type' => 'hidden',
            '#title' => t('Rule id'),
            '#default_value' => $rid,
        ];

        $form['field_code_match_id'] = [
            '#type' => 'hidden',
            '#title' => t('Field code match id'),
            '#default_value' => $fcm_id,
        ];

        $form['string_match'] = [
            '#type' => 'textfield',
            '#title' => t('String match'),
        ];

        $form['tid'] = [
            '#type' => 'select',
            '#title' => 'Term',
            '#options' => $this->getAllTermsByField($fcm['field_name'], $rule['entity_bundle'], $rule['entity_type']),
        ];


        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Save'),
            '#button_type' => 'primary',
        );

        return $form;
    }

    /**
     * Returns a unique string identifying the form.
     *
     * @return string
     *   The unique string identifying the form.
     */
    public function getFormId()
    {
        return 'create_ite_fc_match';
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        $rule_repo = new Rule();
        $rule = $rule_repo->find($form_state->getValue('rule_id'));
        $fcm_repo = new FieldCodeMatch();
        $fcm = $fcm_repo->find($form_state->getValue('field_code_match_id'));

        $tid = $form_state->getValue('tid');

        $term = \Drupal\taxonomy\Entity\Term::load($tid);

        $data = [
            'field_code_match_id' => $form_state->getValue('field_code_match_id'),
            'string_match' => $form_state->getValue('string_match'),
            'tid' => $tid,
            'term_name' => $term->getName(),
        ];

        $item_repo = new ItemMatch();
        $item_repo->insert($data);

        $response = new RedirectResponse("/admin/ite/rules/{$rule['id']}/config/{$fcm['id']}/config");
        $response->send();
        return;
    }

    protected function getAllFieldsByBundle($bundle, $entity){
        $result = [];

        foreach (\Drupal::service('entity_field.manager')->getFieldDefinitions($entity, $bundle) as $field_name => $field_definition) {
            $result[$field_name] = $field_definition->getLabel().'';
            
        }
        
        return $result;
    }

    protected function getFieldTypeByName($field_name, $bundle, $entity){
        foreach (\Drupal::service('entity_field.manager')->getFieldDefinitions($entity, $bundle) as $key => $field_definition) {
            //$result[$field_name] = $field_definition->getLabel().'';
            if($field_name == ($field_definition->getName())){
                return $field_definition->getType();
            }
        }
        return null;
    }

    protected function getFieldDefinitionByName($field_name, $bundle, $entity){
        foreach (\Drupal::service('entity_field.manager')->getFieldDefinitions($entity, $bundle) as $key => $field_definition) {
            //$result[$field_name] = $field_definition->getLabel().'';
            if($field_name == ($field_definition->getName())){
                return $field_definition;
            }
        }
        return null;
    }

    protected function getAllTermsByField($field_name, $bundle, $entity) {
        $result = [];

        $field_definition = $this->getFieldDefinitionByName($field_name, $bundle, $entity);

        $vocabulary_name = array_shift($field_definition->getSetting('handler_settings')['target_bundles']);
        $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vocabulary_name);
        foreach ($terms as $k=>$term){
            $result[$term->tid] = $term->name;
        }

        return $result;
    }
}