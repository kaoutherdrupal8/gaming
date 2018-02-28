<?php

namespace Drupal\hello\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a form that configures forms module settings.
 */
class ColorForm extends ConfigFormBase {

  public function __construct(EntityTypeManagerInterface $entityTypeManager){
    $this->entityTypeManager = $entityTypeManager;
  }

  public static function create(ContainerInterface $container){
    return new static(
      $container->get('entity_type.manager')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'color_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'hello.config',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('hello.config');
    
    $form['form-color'] = array(
      '#type' => 'select',
      '#title' => $this->t('Choose a block color'),
      '#options' => array(
                         'blue-class' => $this->t('Blue'),
                         'rose-class' => $this->t('Rose'),
                         'orange-class' => $this->t('Orange'),
      ),
      '#default_value'  => $this->config('hello.config')->get('color'),

    );
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  
    $value = $form_state->getValue('form-color');
    $this->config('hello.config')
      ->set('color', $value)
      ->save();

      $this->entityTypeManager->getViewBuilder('block')->resetCache();

     parent::submitForm($form, $form_state);
    
  }

}