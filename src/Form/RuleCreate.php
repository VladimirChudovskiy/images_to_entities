<?php

namespace Drupal\images_to_entities\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\images_to_entities\Repository\Rule;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RuleCreate extends FormBase {

    protected function getAllEntitiesAndBundlesArray(){
        $entities = \Drupal::service('entity_type.bundle.info')->getAllBundleInfo();
        $result = [];
        foreach ($entities as $name=>$children){
            $result[$name] = [];
            foreach ($children as $bundle_name=>$item){
                if(is_object($item['label'])){
                    $result[$name][$bundle_name] = $item['label']->__toString();
                }else{
                    $result[$name][$bundle_name] = $item['label'];
                }
            }
        }

        return $result;
    }

    public function buildForm(array $form, FormStateInterface $form_state, AccountInterface $user = NULL) {
        //$entities_and_bundles = $this->getAllEntitiesAndBundlesArray();

        $form['name'] = [
            '#type' => 'textfield',
            '#title' => t('Name'),
        ];

        $form['entity_bundle'] = [
            '#type' => 'select',
            '#title' => 'Entity bundle',
            '#options' => $this->getAllEntitiesAndBundlesArray()
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
        return 'create_ite_rule';
    }

    /**
     * Form submission handler.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $bundle_name = $form_state->getValue('entity_bundle');

        $data = [
            'name' => $form_state->getValue('name'),
            'entity_type' => $this->getEntityNameByBundleName($bundle_name),
            'entity_bundle' => $form_state->getValue('entity_bundle'),
        ];
//        echo "<pre>";
//        print_r($data);
//        echo "</pre>";
//        die('.');

        $rule_repo = new Rule();
        $rule_repo->insert($data);

        $response = new RedirectResponse("/admin/ite/rules");
        $response->send();
        return;
    }

    protected function getEntityNameByBundleName($bundle_name){
        $all = $this->getAllEntitiesAndBundlesArray();

        foreach ($all as $entity_name=>$bundles){
            foreach ($bundles as $name=>$human_name){
                if($name === $bundle_name){
                    return $entity_name;
                }
            }
        }
    }

}