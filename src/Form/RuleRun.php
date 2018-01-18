<?php

namespace Drupal\images_to_entities\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\images_to_entities\Repository\Image;
use Drupal\images_to_entities\Repository\Rule;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RuleRun extends FormBase {

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

    public function buildForm(array $form, FormStateInterface $form_state, AccountInterface $user = NULL, $rid = NULL) {
        $rule_repo = new Rule();
        $rule = $rule_repo->find($rid);

        $form['rule_id'] = [
            '#type' => 'hidden',
            '#title' => t('Rule id'),
            '#default_value' => $rid,
        ];

        $form['images'] = array(
            '#type' => 'managed_file',
            '#upload_location' => 'public://images/',
            '#multiple' => TRUE,
            '#upload_validators' => array(
                'file_validate_extensions' => array('png gif jpg jpeg'),
                'file_validate_size' => array(25600000),
                //'file_validate_image_resolution' => array('800x600', '400x300'),
            ),
            '#preview' => TRUE,
        );

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
        return 'run_ite_rule';
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
        $files = \Drupal::database()->select('file_managed', "t")
            ->fields("t")
            ->condition('fid', $form_state->getValue('images'), 'IN')
            ->execute()
            ->fetchAll();

        $data = [];
        foreach ($files as $item){
            $data[] = [
                'rule_id' => $form_state->getValue('rule_id'),
                'fid' => $item->fid,
                'filename' => $item->filename,
                'uri' => $item->uri,
                'uri' => $item->uri,
                'alt' => '',
                'title' => '',
            ];
        }

        $image_repo = new Image();
        foreach ($data as $item){
            $image_repo->insert($item);
        }

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