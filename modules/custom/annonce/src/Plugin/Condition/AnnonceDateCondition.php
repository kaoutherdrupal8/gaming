<?php

namespace Drupal\annonce\Plugin\Condition;


use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\Core\Plugin\Context\ContextDefinition;

/**
* Provides a 'Date' condition to enable a condition based in module selected status.
*
* @Condition(
*   id = "date",
*   label = @Translation("Date"),
*   context = {
*     "date_format" = @ContextDefinition("entity:date_format", required = FALSE , label = @Translation("date_format"))
*   }
* )
*
*/
class AnnonceDateCondition extends ConditionPluginBase {

/**
* {@inheritdoc}
*/
public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
{
    return new static(
    $configuration,
    $plugin_id,
    $plugin_definition
    );
}

/**
 * Creates a new AnnonceDateCondition object.
 *
 * @param array $configuration
 *   The plugin configuration, i.e. an array with configuration values keyed
 *   by configuration option name. The special key 'context' may be used to
 *   initialize the defined contexts by setting it to an array of context
 *   values keyed by context names.
 * @param string $plugin_id
 *   The plugin_id for the plugin instance.
 * @param mixed $plugin_definition
 *   The plugin implementation definition.
 */
 public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
 }

 /**
   * {@inheritdoc}
   */
 public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    /*$timestamp = \Drupal::time()->getCurrentTime();
    $currentTimeFormater = format_date($timestamp, 'custom','G\hi s\s');*/
    $form = parent::buildConfigurationForm($form, $form_state);
     /*// Sort all modules by their names.
     $modules = system_rebuild_module_data();
     uasort($modules, 'system_sort_modules_by_info_name');

     $options = [NULL => t('Select a module')];
     foreach($modules as $module_id => $module) {
         $options[$module_id] = $module->info['name'];
     }*/

     /*$form['module'] = [
         '#type' => 'select',
         '#title' => $this->t('Select a module to validate'),
         '#default_value' => $this->configuration['module'],
         '#options' => $options,
         '#description' => $this->t('Module selected status will be use to evaluate condition.'),
     ];*/

     $form['dateDebut'] = [
             '#type' => 'date',
             '#title' => $this->t('Select date de debut'),
             //'#options' => array('dateDebut' => $currentTimeFormater),
             //'#default_value' => $this->configuration['date_debut'],
             '#description' => $this->t('choisir date de debut .'),
         ];

     $form['dateFin'] = [
             '#type' => 'date',
             '#title' => $this->t('Select date de FIN'),
             //'#options' => array('dateFin' => $currentTimeFormater),
             //'#default_value' => $this->configuration['date_fin'],
             '#description' => $this->t('choisir date de FIN .'),
         ];






       $form['negate']['#access'] = FALSE;

       return $form;
 }

/**
 * {@inheritdoc}
 */
 public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
   $this->configuration['dateDebut'] = $form_state->getValue('dateDebut');
   $this->configuration['dateFin'] = $form_state->getValue('dateFin');
     //$this->configuration['module'] = $form_state->getValue('module');
     parent::submitConfigurationForm($form, $form_state);
 }

/**
 * {@inheritdoc}
 */
 public function defaultConfiguration() {
    return ['module' => ''] + parent::defaultConfiguration();
 }

/**
  * Evaluates the condition and returns TRUE or FALSE accordingly.
  *
  * @return bool
  *   TRUE if the condition has been met, FALSE otherwise.
  */
  public function evaluate() {
   $dateDebut = strtotime($this->configuration['dateDebut']);
   $dateFin = strtotime($this->configuration['dateFin']);

   // Aucune date.
   if (empty($dateDebut) && empty($dateFin)) {
     return TRUE;
   }
   // Date de début uniquement.
   if (!empty($dateDebut) && empty($dateFin)) {
     if ($dateDebut<= time()) {
       return TRUE;
     }
     else return FALSE;
   }
   // Date de fin uniquement.
   if (empty($dateDebut) && !empty($dateFin)) {
     if ($dateDebut >= time()) {
       return TRUE;
     }
   }
   // Date de début et de fin.
   if (!empty($dateDebut) && !empty($dateFin)) {
     if ($dateDebut <= time() && $dateFin>= time()) {
       return TRUE;
     }
   }

   else return FALSE;
 }
      /*if (empty($this->configuration['module']) && !$this->isNegated()) {
          return TRUE;
      }

      $module = $this->configuration['module'];
      $modules = system_rebuild_module_data();

      return $modules[$module]->status;
  }*/

/**
 * Provides a human readable summary of the condition's configuration.
 */
 public function summary()
 {

  return $this->t('Date visibility condition.');
     /*$module = $this->getContextValue('module');
     $modules = system_rebuild_module_data();

     $status = ($modules[$module]->status)?t('enabled'):t('disabled');

     return t('The module @module is @status.', ['@module' => $module, '@status' => $status]);*/
 }

}
