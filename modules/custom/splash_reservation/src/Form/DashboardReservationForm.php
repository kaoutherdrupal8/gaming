<?php

namespace Drupal\splash_reservation\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;



use Drupal\Component\Render\FormattableMarkup;

/*use Drupal\node\NodeInterface;*/



/*use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\BeforeCommand;
use Drupal\Core\Ajax\AfterCommand;*/

/*use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;*/

/**
 * Class DashboardReservationForm.
 */
class DashboardReservationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dashboard_reservation_form';
  }

  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'splash_reservation.dashboard',
    ];
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $allCurrentReservation = $this->getData();

    $options = [];
    $emptyMessage = $this->t("Vous n'avez aucune réservation en cours ! ");
    $message = $this->t("Vous avez actuellement %number réservation@plural en cours !", array(
        "%number" => count($allCurrentReservation),
        "@plural" => $this->plural(count($allCurrentReservation))
      ) 
    );


    $header = array(
      "date" => $this->t('Date'), 
      "activity" => $this->t('Activity'), 
      "nb_inscris" => $this->t('Register'), 
      //"btn" => $this->t('Update Number Person')
    );
    
    foreach($allCurrentReservation as $reservation){

      $options[$reservation->srid] = array(
        "date" => format_date( $reservation->date_resa, '', 'l j F Y - H:i'),
        "activity" => $this->getActivityLink($reservation->nid),
        "nb_inscris" => $this->NumberRegister($reservation->nb_inscript),
        //"btn" => $this->getButtonUpdate($reservation->srid)
      );

    }

    $form['table'] = array(
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => $options,
      '#cache' => ['disabled' => TRUE],
      '#multiple' => true,
      '#empty' => $emptyMessage,
    );

    if ( !empty($allCurrentReservation) ) {
      $form['table']['#caption'] = $message;
    }

    $form['submit'] = [
      '#type'     => 'submit',
      '#value'    =>  $this->t('Remove checked bookings')
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  

  public function validateForm(array &$form, FormStateInterface $form_state){

    $count = 0;
    foreach ($form_state->getValue('table') as $row => $reservationID) {
      if ($reservationID > 0) {
        $count++;
      }
    }

    if ( $count === 0 ) {
      $form_state->setErrorByName(
        'table', $this->t("Vous n'avez rien coché, il n'y a donc aucune réservation à annuler !")
      );
    }

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $count = 0;
    foreach ($form_state->getValue('table') as $row => $reservationID) {
      if ($reservationID > 0) {
        $this->unsetReservation($reservationID);
        $count++;
      }
      
    }

    $alone    = "La réservation cochée a bien été supprimée !";
    $multiple = "Les réservations cochées ont bien été supprimées !";
    $message = $this->t($alone);

    if ( $count > 1 ) {
      $message = $this->t($multiple);
    }
    
    return drupal_set_message( $message, 'status');

  }

  public function getDatabase() {
    return \Drupal::database();
  }

  public function getReservation() {
    return $this->getDatabase()->select('splash_reservation','sr');
  }

  public function unsetReservation($reservationID) {
    return $this->getDatabase()->delete('splash_reservation')
            ->condition( 'srid', $reservationID )
            ->execute();
  }

  public function getCurrentUserID() {
    return \Drupal::currentUser()->id();
  }

  public function getCurrentTime() {
    return \Drupal::time()->getCurrentTime();
  }

  public function getCurrentLanguageID() {
    return \Drupal::languageManager()->getCurrentLanguage()->getId();
  }

  public function getActivityLink($id) {
    return \Drupal::entityTypeManager()->getStorage("node")->load($id)->toLink();
  }

  public function getButtonUpdate($reservationID) {
    $buttonUpdate = new FormattableMarkup('<a class="uk-button uk-button-primary" href="/@lang/update/reservation/@id">@text</a>', 
          array( 
            '@lang' => $this->getCurrentLanguageID(),
            '@id' => $reservationID, 
            '@text' => $this->t("Update") 
          ) 
    );
    return $buttonUpdate;
  }

  public function plural($int){
    if ($int >= 2) {
      return "s";
    }
    return "";
  }

  public function getData() {

    $query = $this->getReservation()
          ->fields('sr', ['srid', 'nid', 'nb_inscript', 'date_resa'])
          ->condition( 'uid',  $this->getCurrentUserID() )
          ->condition( 'date_resa',  $this->getCurrentTime()+600 , '>=')
          ->orderBy('date_resa', 'ASC')
          ->execute();

    return $query->fetchAll();

  }


  public function NumberRegister($variable){

    switch ($variable) {
      case 1:
        $response = $this->t("Seul");
        break;
      
      case 2:
        $response = $this->t("À deux");
        break;
      
      case 3:
        $response = $this->t("À trois");
        break;
      
      case 4:
        $response = $this->t("À quatre");
        break;
      
      case 5:
        $response = $this->t("À cinq");
        break;
      
      case 6:
        $response = $this->t("À six");
        break;

      default:
        $response = $this->t("Problem to show how much");
        break;
    };

    return $response;
  }





}
