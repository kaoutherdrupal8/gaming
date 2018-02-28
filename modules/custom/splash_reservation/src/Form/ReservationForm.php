<?php

namespace Drupal\splash_reservation\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\node\NodeInterface;
use Drupal\paragraphs\Entity\Paragraph;



use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\BeforeCommand;
use Drupal\Core\Ajax\AfterCommand;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

/**
 * Class ReservationForm.
 */
class ReservationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'reservation_form';
  }

  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'splash_reservation.config',
    ];
  }

  /**
   * Returns a page title.
   */
  public function getTitle(NodeInterface $node = null) {
    $title = $this->t('Réserver l\'activité : %name', array(
                      '%name' => $node->getTitle()
                    ));
    return $title;
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = null) {

    // Create a new hidden field comp_infos
    $form['comp_infos'] = array(
      '#type' => 'hidden',
      '#default_value' => 0
    );

    // Create a new provisoire field nb_inscript
    $form['nb_inscript'] = array(
      '#type'       => 'hidden',
      '#title'      => $this->t("S'inscrire à cette session"),
      '#default_value' => 0
    );

    // Create a new hidden field for validate method
    $form['error'] = array(
      '#type' => 'hidden',
      '#default_value' => 0
    );

    // Create a new hidden field for save if the user already reserved or not a session
    $form['user_already_reserved'] = array(
      '#type' => 'hidden',
      '#default_value' => 0
    );

    // Create a new hidden field for save if the current session have no place
    $form['session_no_place'] = array(
      '#type' => 'hidden',
      '#default_value' => 0
    );

    // Create a new hidden field node_id
    $form['node_id'] = array(
      '#type' => 'hidden',
      '#value' => $node->id()
    );

    // Create a new hidden field place_dispo_activity
    $form['place_dispo_activity'] = array(
      '#type' => 'hidden',
      '#value' => ($node->get('field_places_disponible')->getString() === "") ? 30 : $node->get('field_places_disponible')->getString()
    );

    // Create a new field creneau
    $form['creneau_horaires'] = array(
      '#type'     => 'radios',
      '#title'    => $this->t('Choose your time slot'),
      '#options'  => $this->getAllCreneau($node),
      '#ajax'     =>  array(
                    'callback'  => array($this,'updateDateResaInSelect'),
                    'event'   => 'change'
                  ),
      '#validated' => true,
    );

    $form['date_resa'] = array(
      '#type'     => 'select',
      '#title'    => $this->t('Choose a booking date'),
      '#options'  => array( "wrong-val" => $this->t('Choose an option') ), 
      '#states'   => array(
                    'visible' => array(
                      'input[name="creneau_horaires"]' => array('checked' => TRUE),
                    ),
                  ),
      '#ajax'     =>  array(
                    'callback'  => array($this,'updatePlaceDispoForDateChoosen'),
                    'event'   => 'change'
                  ),
      '#validated' => true,
    );

    $form['submit'] = [
      '#type'     => 'submit',
      '#value'    =>  $this->t('Booking')
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */

  public function validateForm(array &$form, FormStateInterface $form_state, NodeInterface $node = null){

    // Si le formulaire est soumis et que le field creneau_horaires n'a rien de coché
    if ( $form_state->getValue('creneau_horaires') === NULL ) {
      $form_state->setErrorByName(
        'creneau_horaires', $this->t('Select a time slot')
      );
    }
    
    // Si le formulaire est soumis et que le field date_resa n'a rien de coché
    if ( $form_state->getValue('creneau_horaires') !== NULL && $form_state->getValue('date_resa') === "wrong-val" && $form_state->getValue('nb_inscript') == 0 ){
      $form_state->setErrorByName(
        'date_resa', $this->t('Select a date for reservate a session')
      );
      $form_state->setErrorByName(
        'error', $this->t("Une erreur a eu lieu sur cette page, afin de pouvoir réserver votre activité, vous devez raffraichir la page via le boutton ci-dessus !")
      );
    }

    // Si le formulaire est soumis et que l'utilisateur a déjà réservé la session en cours
    if ( $form_state->getValue('creneau_horaires') !== NULL && $form_state->getValue('user_already_reserved') == 1 && $form_state->getValue('date_resa') !== "wrong-val" ){
      $form_state->setErrorByName(
        'user_already_reserved', $this->t('Vous êtes déjà inscrit à cette session, et ne pouvez donc pas vous y inscrire une seconde fois !')
      );
      $form_state->setErrorByName(
        'error', $this->t("Une erreur a eu lieu sur cette page, afin de pouvoir réserver votre activité, vous devez raffraichir la page via le boutton ci-dessus !")
      );
    }

    // Si le formulaire est soumis et que l'utilisateur a déjà réservé la session en cours
    if ( $form_state->getValue('creneau_horaires') !== NULL && $form_state->getValue('session_no_place') == 1 && $form_state->getValue('date_resa') !== "wrong-val" ){
      $form_state->setErrorByName(
        'session_no_place', $this->t("Cette session n'a plus de place disponible, choissisez en une autre !")
      );
      $form_state->setErrorByName(
        'error', $this->t("Une erreur a eu lieu sur cette page, afin de pouvoir réserver votre activité, vous devez raffraichir la page via le boutton ci-dessus !")
      );
    }

    // Si le formulaire est soumis et qu'il bug
    if($form_state->getValue('creneau_horaires') !== NULL && $form_state->getValue('date_resa') !== "wrong-val" && $form_state->getValue('user_already_reserved') == 0 && $form_state->getValue('session_no_place') == 0 && $form_state->getValue('nb_inscript') == 0 ){
      $form_state->setErrorByName(
        'error', $this->t("Une erreur a eu lieu sur cette page, afin de pouvoir réserver votre activité, vous devez raffraichir la page via le boutton ci-dessus !")
      );
    }



  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    $this->setTheReservation($form_state);

    drupal_set_message("Votre réservation a bien été effectué, vous pouvez la modifier depuis cette page !", 'status');

    return $form_state->setRedirect( "user.activity.reservation", array( "user" => $this->getCurrentUserID() ) );

  }

  public function setTheReservation($form_state){

    $database = \Drupal::database();
    $query = $database->insert('splash_reservation')
                ->fields(array(
                  'nid' => $this->getCurrentActivityID($form_state),
                  'uid' => $this->getCurrentUserID(),
                  'date_resa' => $this->getReservationDate($form_state),
                  'nb_inscript' => $this->getNbInscript($form_state),
                ))
                ->execute();

    return $query;
  }


  public function getAllCreneau($node){

    $planning_target_id = $node->get('field_planning')->getValue()[0]['target_id'];
    $paragraph = Paragraph::load($planning_target_id);

    $planLundi      = $paragraph->get('field_lundi')->getValue() ;
    $planMardi      = $paragraph->get('field_mardi')->getValue() ;
    $planMercredi   = $paragraph->get('field_mercredi')->getValue() ;
    $planJeudi      = $paragraph->get('field_jeudi')->getValue() ;
    $plan_vendredi  = $paragraph->get('field_vendredi')->getValue() ;
    $planSamedi     = $paragraph->get('field_samedi')->getValue() ;
    $planDimanche   = $paragraph->get('field_dimanche')->getValue() ;

    $traitementPlan = array(
                        'lundi' => $planLundi, 
                        'mardi' => $planMardi, 
                        'mercredi' => $planMercredi, 
                        'jeudi' => $planJeudi, 
                        'vendredi' => $plan_vendredi, 
                        'samedi' => $planSamedi, 
                        'dimanche' => $planDimanche
                      );
    $allCreneau     = array();

    foreach ($traitementPlan as $day => $horaires) {
      if ( count($horaires) > 0 ) {
        for ($i=0; $i < count($horaires); $i++) { 
          $horaire = $this->convertInHoraire($horaires[$i]['value']);
          $key = $day."_".$horaire;
          $value = ucfirst ( $day ) . " à " . $horaire ;
          $allCreneau[$key] = $value;
        }
      }
    }

    return $allCreneau;
  }


  public function convertInHoraire($time){
    $hTime = 3600;
    $mTime = 60;
    $heures = strlen( intval( $time / $hTime )) === 1 ? "0".intval( $time / $hTime ) : intval( $time / $hTime ) ;
    $minutes = (intval( ( $time % $hTime ) / $mTime ) == 0 ? "00": intval( ( $time % $hTime ) / $mTime )  ) ;
    return $heures.":".$minutes;
  }


  public function updateDateResaInSelect(array &$form, FormStateInterface $form_state){

    $response   = new AjaxResponse();
    $fieldName  = $form_state->getTriggeringElement()['#name'];
    $fieldValue = $form_state->getValue($fieldName);

    // Reset DIV complementary-infos
    $this->setComplementaryInfos($form_state, $response);

    // Set the options to date_resa
    $form['date_resa']['#options'] = $this->getDateResaOptions($fieldValue);
    $response->addCommand( 
      new ReplaceCommand(".form-type-select.form-item-date-resa", $form['date_resa'])
    );

    return $response;

  }


  public function getDateResaOptions($fieldValue){

    $dayWeek    = explode("_",$fieldValue)[0];
    $hour       = explode(":",explode("_",$fieldValue)[1])[0];
    $minute     = explode(":",explode("_",$fieldValue)[1])[1];


    $currentTime      = \Drupal::time()->getCurrentTime();
    $addOneDay        = 3600*24;
    $addOneWeek       = 3600*24*7;

    // For detect day of the week
    for ($i=0; $i < 7; $i++) { 
      if ( $dayWeek === strtolower(format_date( $currentTime + ($addOneDay * $i), '', 'l'))  ) {

        $precisionDay = format_date( $currentTime + ($addOneDay * $i), '', 'Y-m-d');

        break;
      }
    }

    //$datetime = date('Y-m-d H:i', \Drupal::time()->getCurrentTime());
    $datetime = $precisionDay." ".$hour.":".$minute;
    $firstTimeStamp = strtotime($datetime);

    //Vérifier si la proposition de timestamps est supérieure au current timestamps + 1 heure
    if (($currentTime +3600) > $firstTimeStamp ) {
      $firstTimeStamp = $firstTimeStamp + $addOneWeek;
    }
    
    $result = array("wrong-val" => $this->t('Choose an option'));

    //Composer les 5 propositions de date
    for ($i=0; $i < 5; $i++) { 
      $time = $firstTimeStamp + ($addOneWeek * $i);
      $date = format_date( $time, '', 'l j F Y - H:i');
      $result[$time] = $date;
    }

    return $result;
    
  }


  public function setComplementaryInfos($form_state, $response){

    $comp_infos = "<div id='complementary-infos'></div>";
    if ($form_state->getValue('comp_infos') == 0 ) {

      $form['comp_infos'] = array(
        '#name' => 'comp_infos',
        '#type' => 'hidden',
        '#value' => 1
      );

      $response->addCommand( 
        new ReplaceCommand("input[name='comp_infos']", $form['comp_infos'])
      );

      $response->addCommand( 
        new BeforeCommand("#edit-submit", $comp_infos)
      );

    }
    else {

      $response->addCommand(
        new ReplaceCommand("#complementary-infos", $comp_infos)
      );

    }
    return $response;

  }

  public function getTableReservation(){
    $database = \Drupal::database();
    return $database->select('splash_reservation','sr');
  }

  public function getCurrentUser(){
    return \Drupal::currentUser();
  }

  public function getCurrentUserID(){
    return intval( $this->getCurrentUser()->id() );
  }

  public function getCurrentActivityID($form_state){
    return intval( $form_state->getValue("node_id") );
  }

  public function getTotalPlaceDispoActivity($form_state){
    return intval( $form_state->getValue("place_dispo_activity") );
  }

  public function getReservationDate($form_state){
    return intval( $form_state->getValue("date_resa") );
  }

  public function getNbInscript($form_state){
    return intval( $form_state->getValue("nb_inscript") );
  }

  public function isUserFalseChoice($form_state){
    $test = $this->getReservationDate($form_state);
    if ( is_int( $test ) === true && $test > 5000 ) {
      return false;
    }
    return true;
  }

  public function isUserAlreadyReserved($form_state){

    $dbSplashReservation  = $this->getTableReservation();

    $query = $dbSplashReservation
          ->fields('sr', [ 'nid', 'uid', 'date_resa', 'nb_inscript'])
          ->condition( 'nid', $this->getCurrentActivityID($form_state) )
          ->condition( 'date_resa', $this->getReservationDate($form_state) )
          ->condition( 'uid',  $this->getCurrentUserID() )
          ->countQuery()
          ->execute();

    return intval( $query->fetchField() );

  }

  public function getNbPlaceSessionActivity($form_state){
    $placeDispoActivity   = $this->getTotalPlaceDispoActivity($form_state);
    $dbSplashReservation  = $this->getTableReservation();

    $query = $dbSplashReservation
          ->fields('sr', [ 'nid', 'uid', 'date_resa', 'nb_inscript'])
          ->condition( 'nid', $this->getCurrentActivityID($form_state) )
          ->condition( 'date_resa', $this->getReservationDate($form_state) )
          ->execute();
    $actualsResa = $query->fetchAll();

    // S'il existe des réservations pour cette activité et à cette date précise,
    // alors contrôller le nombre de place déjà réservé
    if (!empty($actualsResa)) {

      foreach($actualsResa as $actualResa){
        $placeDispoActivity -= $actualResa->nb_inscript;
      }
    }
    return ( $placeDispoActivity <= 0 ? 0 : $placeDispoActivity );
  }

  public function updatePlaceDispoForDateChoosen(array &$form, FormStateInterface $form_state){

    $response = new AjaxResponse();

    // Reset DIV complementary-infos
    $this->setComplementaryInfos($form_state, $response);

    // Vérifier si l'utilisateur a fait un mauvais choix
    if ( $this->isUserFalseChoice($form_state) ) {
      // Phrase d'informations sur la condition vérifié à true
      $str = "Vous êtes déjà inscrits à cette session !";
      $this->showStringInfo($response, $str);

      return $response;
    }

    // Vérifier si l'utilisateur en cours à déjà une réservation sur cette session
    $userAlreadyReserved = $this->isUserAlreadyReserved($form_state);

    // Si l'utilisateur à déjà réservé, alors retourné la réponse
    if ( $userAlreadyReserved > 0 ) {

      // Phrase d'informations sur la condition vérifié à true
      $str = "Vous êtes déjà inscrits à cette session !";
      $this->showStringInfo($response, $str);

      // Stocker l'information de la condition vérifié à true
      $this->setHiddenControlField('user_already_reserved', 1, $response, $form);

      return $response;
    }

    // Sinon continuer et 
    // Stocker l'information de la condition vérifié à false 
    $this->setHiddenControlField('user_already_reserved', 0, $response, $form);

    // Récupérer le nombre de place restantes pour cette session
    $placeDispoActivity = $this->getNbPlaceSessionActivity($form_state);

    // Phrase d'informations du nombre de place restantes
    if($placeDispoActivity <= 0){

      $str = "Il n'y a plus aucune place de disponible pour cette session !";
      $arg = $placeDispoActivity;
      $strArg = NULL;

    }
    elseif ( $placeDispoActivity === 1 ) {

      $str = "Place Restante : @nb";
      $arg = $placeDispoActivity;
      $strArg = "@nb";

    }
    else{

      $str = "Places Restantes : @nb";
      $arg = $placeDispoActivity;
      $strArg = "@nb";

    }
    $this->showStringInfo($response, $str, $arg, $strArg);
    


    // S'il n'y a plus de places, retourner la phrase d'informations seulement
    // et modifier le field hidden 
    if ($placeDispoActivity < 1 ) {

      // Stocker l'information de la condition vérifié à true
      $this->setHiddenControlField('session_no_place', 1, $response, $form);

      return $response;
    }

    // Sinon continuer et 
    // Stocker l'information de la condition vérifié à false
    $this->setHiddenControlField('session_no_place', 0, $response, $form);

    // Proposer de s'inscrire à plusieurs s'il y a suffisament de place de disponible
    $this->proposeSeveralPeopleBooking($response, $form, $placeDispoActivity);

    return $response;

  }


  public function setHiddenControlField($fieldName, $value, $response, $form){

    $form[$fieldName] = array(
      '#name' => $fieldName,
      '#type' => 'hidden',
      '#value' => $value
    );

    $response->addCommand( 
      new ReplaceCommand("input[name='".$fieldName."']", $form[$fieldName] )
    );

    return $response;
  }


  public function proposeSeveralPeopleBooking($response, $form, $placeDispoActivity){

    $nbInscriptionDispo = $placeDispoActivity >= 6  ? 6 : $placeDispoActivity;
    $inscriptionOptions = array();
    $propositionOptions = array(
      $this->t("Seul"),
      $this->t("À deux"),
      $this->t("À trois"),
      $this->t("À quatre"),
      $this->t("À cinq"),
      $this->t("À six")
    );

    for ($i=0; $i < $nbInscriptionDispo ; $i++) { 
      $inscriptionOptions[$i+1] = $propositionOptions[$i];
    }

    $form['nb_inscript'] = array(
      '#type'       => 'select',
      '#name'       => 'nb_inscript',
      '#title'      => $this->t("S'inscrire à cette session"),
      '#options'    => $inscriptionOptions,
      '#validated'  => true
    );

    $response->addCommand( 
      new AfterCommand("#show-string-info", "<div id='nb_inscript'></div>")
    );
    // For delete the same field is hidden, available the first time
    $response->addCommand( 
      new ReplaceCommand("input[name='nb_inscript']", "")
    );
    $response->addCommand( 
      new HtmlCommand("#nb_inscript", $form['nb_inscript'])
    );

    return $response;
  }


  public function showStringInfo($response, $str, $arg = NULL, $strArg = NULL){

    if ( $arg === NULL || $arg <= 0 ) {
      $stringTrans = $this->t($str);
    }
    else {
      $stringTrans = $this->t($str, array( $strArg => $arg));
    }

    $element = "<p id='show-string-info'><strong>".$stringTrans."</strong></p>";

    $response->addCommand( 
      new HtmlCommand("#complementary-infos", $element)
    );

    return $response;

  }



}
