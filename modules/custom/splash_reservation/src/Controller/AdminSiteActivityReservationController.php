<?php

namespace Drupal\splash_reservation\Controller;

use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\Core\Database\Connection;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Component\Datetime\Time;
use Drupal\Core\Entity\EntityTypeManagerInterface;


class AdminSiteActivityReservationController extends ControllerBase{


	protected $database;
	protected $getReservation;
	protected $languageID;
	protected $currentTime;
	protected $entityTMNode;
	protected $entityTMUser;


	public function __construct(Connection $database, LanguageManagerInterface $language, Time $time, EntityTypeManagerInterface $entityTypeManager){
		$this->database 		= $database;
		$this->getReservation 	= $database->select('splash_reservation','sr');
		$this->languageID 		= $language->getCurrentLanguage()->getId();
		$this->currentTime 		= $time->getCurrentTime();
		$this->entityTMNode 	= $entityTypeManager->getStorage('node');
		$this->entityTMUser 	= $entityTypeManager->getStorage('user');
	}


	public static function create(ContainerInterface $container){
		return new static(
			$container->get("database"),
			$container->get("language_manager"),
			$container->get('datetime.time'),
			$container->get("entity_type.manager")
		);
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


	public function getActualData() {

		$query = $this->getReservation
				->fields('sr', ['srid', 'nid', 'uid', 'nb_inscript', 'date_resa'])
				->condition( 'date_resa',  $this->currentTime+600 , '>=')
				->orderBy('date_resa', 'ASC')
				->execute();

		return $query->fetchAll();

	}

	public function getOldData() {

		$query = $this->getReservation
				->fields('sr', ['srid', 'nid', 'uid', 'nb_inscript', 'date_resa'])
				->condition( 'date_resa',  $this->currentTime , '<')
				->orderBy('date_resa', 'DESC')
				->execute();

		return $query->fetchAll();

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


	public function getUserName($userID) {
		return $this->entityTMUser->load( $userID)->getDisplayName();
		/*$query = $this->database->select('users_field_data','us')
				->fields('us', ['uid', 'name'])
				->execute();

		$userListName = $query->fetchAll();

		foreach ($userListName as $key => $value) {
			if ($value->uid == $userID) {
				return $value->name;
			}
		}*/
	}

	public function old() {

	  	$allOldReservation = $this->getOldData();

	    $emptyMessage = $this->t("Il n'y a aucune réservation déjà passé ! ");
	    $message = $this->t("Il y a %number réservation@plural déjà passé !", array(
	        "%number" => count($allOldReservation),
	        "@plural" => $this->plural(count($allOldReservation))
	      ) 
	    );

		$header = array( $this->t('Date'), $this->t('Activity'), $this->t('User Name'), $this->t('Register') );
		$options = array();

	 	foreach($allOldReservation as $reservation){
			$options[] = array(
				format_date( $reservation->date_resa, '', 'l j F Y - H:i'),
				$this->getActivityLink($reservation->nid),
				$this->getUserName($reservation->uid),
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

	public function actual() {

	  	$allActualReservation = $this->getActualData();

	    $emptyMessage = $this->t("Il n'y a aucune réservation à venir ! ");
	    $message = $this->t("Il y a %number réservation@plural à venir !", array(
	        "%number" => count($allActualReservation),
	        "@plural" => $this->plural(count($allActualReservation))
	      ) 
	    );

		$header = array( $this->t('Date'), $this->t('Activity'), $this->t('User Name'), $this->t('Register') );
		$options = array();

	 	foreach($allActualReservation as $reservation){
			$options[] = array(
				format_date( $reservation->date_resa, '', 'l j F Y - H:i'),
				$this->getActivityLink($reservation->nid),
				$this->getUserName($reservation->uid),
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
	

}
