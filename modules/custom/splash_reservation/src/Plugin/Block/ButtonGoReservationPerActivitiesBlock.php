<?php

namespace Drupal\splash_reservation\Plugin\Block;

use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'ButtonGoReservationPerActivities' Block.
 *
 * @Block(
 *   id = "button_go_reservation_per_activities_block",
 *   admin_label = @Translation("Booking this activity"),
 *   category = @Translation("On the page of an activities, show a button for go to reservate this current activity"),
 * )
 */
class ButtonGoReservationPerActivitiesBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {
	$routeMatchParam   = \Drupal::routeMatch()->getParameter('node');
  $userId            = \Drupal::currentUser()->id();

	$currentActivityName 	 = $routeMatchParam->getTitle();
	$currentActivityId     = $routeMatchParam->id();
	$currentLangId 			   = $routeMatchParam->language()->getId();


  if ($userId == 0) {
    //$textButton   = $this->t('Se connecter');
    $textButton   = $this->t('Login');
    $linkButton   = '/'.$currentLangId.'/user/login';
    //$description  = $this->t("Pour pouvoir réserver l'activité %activityName, merci de vous identifier avant !");
    $description  = $this->t('To be able to book the %activityName activity, please login before !', array(
        '%activityName' => $currentActivityName
    ));
  } else {
    //$textButton   = $this->t('Réserver cette activité');
    $textButton   = $this->t('Booking @name', array("@name" => $currentActivityName));
    $linkButton   = '/'.$currentLangId.'/reservation/activity/'.$currentActivityId;
    //$description  = $this->t('Cliquez sur le boutton pour réserve l\'activité %activityName');
    $description  = $this->t('Click on the button to reserve the %activityName activity !', array(
        '%activityName' => $currentActivityName
    ));
  }


    $dataBlock[] = array(
      '#theme'  => 'btn_go_resa_per_activities',
      '#data'   => array(
					"textButton"	=> $textButton,
					"linkButton"	=> $linkButton,
					"description"	=> $description
				)
    );

    return array($dataBlock);

  }


}