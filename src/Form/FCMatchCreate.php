<?php

namespace Drupal\images_to_entities\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\images_to_entities\Repository\FieldCodeMatch;
use Drupal\images_to_entities\Repository\Rule;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FCMatchCreate extends FormBase {


    public function buildForm(array $form, FormStateInterface $form_state, AccountInterface $user = NULL, $rid = NULL) {
        $rule_repo = new Rule();
        $rule = $rule_repo->find($rid);

        $form['rule_id'] = [
            '#type' => 'hidden',
            '#title' => t('Rule id'),
            '#default_value' => $rid,
        ];

        $form['name'] = [
            '#type' => 'textfield',
            '#title' => t('Name'),
        ];

        $form['field_name'] = [
            '#type' => 'select',
            '#title' => 'Field name',
            '#options' => $this->getAllFieldsByBundle($rule['entity_bundle'], $rule['entity_type']),
        ];

        $form['code'] = [
            '#type' => 'textarea',
            '#title' => t('Code'),
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

        $field_name = $form_state->getValue('field_name');

        $data = [
            'name' => $form_state->getValue('name'),
            'rule_id' => $form_state->getValue('rule_id'),
            'field_name' => $field_name,
            'field_type' => $this->getFieldTypeByName($field_name, $rule['entity_bundle'], $rule['entity_type']),
            'code' => $form_state->getValue('code'),
        ];

        $fm_match_repo = new FieldCodeMatch();
        $fm_match_repo->insert($data);

        $response = new RedirectResponse("/admin/ite/rules/".$rule['id'].'/config');
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
}