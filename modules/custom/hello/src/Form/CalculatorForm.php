<?php

namespace Drupal\hello\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\CssCommand;

/**
* Implement a hello form
**/

class CalculatorForm extends FormBase{

    public function getFormID(){
      return 'hello_form';
    }

//Insérer champ texte:
    public function buildForm(array $form, FormStateInterface $form_state){


     // Champ destiné à afficher le résultat du calcul.
    if (isset($form_state->getRebuildInfo()['result'])) {
      $form['result'] = array(
        '#markup' => '<h2>' . $this->t('Result: ') . $form_state->getRebuildInfo()['result'] . '</h2>',
      );
    }
    // le formulaire:
      $form['champ1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First value'),
      '#description' => $this->t('Enter first value.'),
      '#required' => TRUE,
      '#default_value' => '3',
      '#ajax'          => array(
        'callback'  => array($this, 'AjaxValidateNumeric'),
        'event'     => 'change',
        ),
      '#prefix' => '<span id="error-message-value1"></span>',
      ];


// insérer bouton radio
     $form['operation'] = array(
    '#type' => 'radios',
    '#title' => t('Operation'),
    '#description' => $this->t('Choose operation for processing.'),
    '#options' => array('Ajouter'=>t('Ajouter'),'Soustract'=>t('Soustract'),'Multiply'=>t('Multiply'),'Divide'=>t('Divide')),
    '#default_value' => 'Ajouter',
);
//inserer second champ:

$form['champ2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Second Value'),
      '#description' => $this->t('Enter second value.'),
      '#required' => TRUE,
      '#maxlenght' => '128',
      '#size' => '40',
      '#prefix' => '<span id="error-message-value2"></span>',
      ];

//print($form_values['topic']);
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('calculate'),

    ];
      return $form;
    }


    public function validateForm(array &$form, FormStateInterface $form_state){
    	$value_1 =  $form_state->getValue('champ1');
    	$value_2 =  $form_state->getValue('champ2');
    	$operation =  $form_state->getValue('operation');
        if (!is_numeric($value_1)) {
        $form_state->setErrorByName('champ1', $this->t('First value must be numeric!'));
        }

        if (!is_numeric($value_2)) {
        $form_state->setErrorByName('champ2', $this->t('Second value must be numeric!'));
        }
       if ($value_2 == '0' && $operation == 'Divide') {
       $form_state->setErrorByName('champ2', $this->t('Cannot divide by zero!'));
       }

       unset($form['result']);
    	
    }

    public function validateTextAjaxForm(array &$form, FormStateInterface $form_state){
         

    $response = new AjaxResponse();

    // print_r(json_encode($form_state->getTriggeringElement()));

    $field = $form_state->getTriggeringElement()['#name'];
    $css = ['border' => '2px solid green'];
    $message = $this->t('OK!');
    if (!is_numeric($form_state->getValue($field))) {
      $css = ['border' => '2px solid red'];
      $message = $this->t('%field must be numeric!', array('%field' => $form[$field]['#title']));
    }

    $response->AddCommand(new CssCommand("[name=$field]", $css));
    $response->AddCommand(new HtmlCommand('#error-message-' . $field, $message));


    /*$css = ['border' => '6px solid red'];
    $message = 'Ajax message: ' . $form_state->getValue('text');
*/
   /* if ($value_2 == '0' && $operation == 'Divide') {
      $text = 'Numero zero';
      $color = 'red';
    } else {
      $text = 'Pas zero';
      $color = 'green';
    }*/
    return $response;
    	}

    public function submitForm(array &$form, FormStateInterface $form_state){
    	$value_1 =  $form_state->getValue('champ1');
    	$value_2 =  $form_state->getValue('champ2');
    	$operation =  $form_state->getValue('operation');
        

        $resultat = '';
        switch ($operation) {
        	case 'Ajouter':
        		$resultat = $value_1 + $value_2 ;
        		break;
        	case 'Soustract':
        		$resultat = $value_1 - $value_2 ;
        		break;
        	case 'Multiply':
        		$resultat = $value_1 * $value_2 ;
        		break;
        	case 'Divide':
        		$resultat = $value_1 / $value_2 ;
        		break;
        }

         //drupal_set_message($value_1);
         //drupal_set_message($value_2);
         //drupal_set_message($operation);
        /*kint($operation);
        die();*/

         // On passe le résultat.
         $form_state->addRebuildInfo('result', $resultat);
         // Reconstruction du formulaire avec les valeurs saisies.
         $form_state->setRebuild();
    	
    }


}