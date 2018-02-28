<?php

namespace Drupal\splash_reservation\Controller;

use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\Core\Database\Connection;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Component\Datetime\Time;
use Drupal\Core\Entity\EntityTypeManagerInterface;


class ActivitiesReservationController extends ControllerBase{


	protected $database;
	protected $getReservation;
	protected $languageID;
	protected $currentTime;
	protected $entityTMNode;


	public function __construct(Connection $database, LanguageManagerInterface $language, Time $time, EntityTypeManagerInterface $entityTypeManager){
		$this->database 		= $database;
		$this->getReservation 	= $database->select('splash_reservation','sr');
		$this->languageID 		= $language->getCurrentLanguage()->getId();
		$this->currentTime 		= $time->getCurrentTime();
		$this->entityTMNode 	= $entityTypeManager->getStorage('node');
	}


	public static function create(ContainerInterface $container){
		return new static(
			$container->get("database"),
			$container->get("language_manager"),
			$container->get('datetime.time'),
			$container->get("entity_type.manager")
		);
	}



	public function activitiesReservationHistory(){


	  	$allOldReservation = $this->getData();

	    $emptyMessage = $this->t("Vous n'avez aucune réservation étant déjà passé ! ");
	    $message = $this->t("Vous avez effectué %number réservation@plural, qui sont déjà passé !", array(
	        "%number" => count($allOldReservation),
	        "@plural" => $this->plural(count($allOldReservation))
	      ) 
	    );

		$header = array( $this->t('Date'), $this->t('Activity'), $this->t('Register') );
		$options = array();

	 	foreach($allOldReservation as $reservation){
			$options[] = array(
				format_date( $reservation->date_resa, '', 'l j F Y - H:i'),
				$this->getActivityLink($reservation->nid),
				$this->NumberRegister($reservation->nb_inscript)
			);
		}


		$output["table"] = array(
			'#theme' => 'table',
			'#header' => $header,
			'#cache' => ['disabled' => TRUE],
			'#rows' => $options,
			'#empty' => $emptyMessage
		);

	    if ( !empty($options) ) {
	      $output["table"]['#caption'] = $message;
	    }

		return array($output);

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


	public function getData() {

		$query = $this->getReservation
				->fields('sr', ['srid', 'nid', 'nb_inscript', 'date_resa'])
				->condition( 'uid',  $this->getCurrentUserID() )
				->condition( 'date_resa',  $this->currentTime , '<')
				->orderBy('date_resa', 'DESC')
				->execute();

		return $query->fetchAll();

	}


	public function getCurrentUserID() {
		return \Drupal::currentUser()->id();
	}


	public function plural($int){
		if ($int >= 2) {
			return "s";
		}
		return "";
	}


	public function getActivityLink($activityID) {
		return $this->entityTMNode->load( $activityID)->toLink();
	}


}
