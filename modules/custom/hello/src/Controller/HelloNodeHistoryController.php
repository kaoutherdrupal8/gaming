<?php

namespace Drupal\hello\Controller;

use Drupal\Core\Controller\ControllerBase;

use Drupal\node\NodeInterface;


class HelloNodeHistoryController extends ControllerBase{

	public function nodeUpdateHistory(NodeInterface $node){

		if ( intval($node->id()) < 0 || !is_int( intval($node->id()) ) ) {
			//return $build[]['#markup'] = "Ce contenu n'existe pas !";
			
			return $build =  array(
				'#markup' => t("Ce contenu n'existe pas !")
			);
		}
		
  		$database = \Drupal::database();

  		$dbNodeHistory = $database->select('hello_node_history','hnh');

  		$query = $dbNodeHistory
  					->fields('hnh', ['nid', 'uid', 'update_time'])
  					->condition( 'nid', intval($node->id()) )
  					->orderBy('update_time', 'DESC')
  					->execute();

  		$histories = $query->fetchAll();
       
      $storage = \Drupal::entityTypeManager()->getStorage("node");
      $nodeTitle = $storage->load( $node->id() )->getTitle(); 


      if (count($histories) == 0) {
        $message = $this->t("The node <strong>%nodeTitle</strong> has not been update", 
       array(
       "%nodeTitle" => $nodeTitle
       )
      );
    } else {
      $message = $this->t("The node <strong>%nodeTitle</strong> has been update <strong>%nbUpdate times</strong>", 
        array(
          "%nodeTitle" => $nodeTitle,
          "%nbUpdate" => count($histories)
        )
      );
    }

  	if (empty($histories)) {
			return array(
      '#theme' => 'hello_node_history',
      '#message' => $message
    );
				//'#markup' => $this->t("Ce contenu n'a subit aucune modification !")
		
  		}

     $info[] = array(
      '#theme' => 'hello_node_history',
      '#message' => $message
    );
  		
		$header = ['#', $this->t('Author'), $this->t('Update Time')];
		$dataTable = [];

		$id = 1;
  		foreach($histories as $history){

  			$dataTable[] = [
  				$id, 
  				user_load($history->uid)->getDisplayName(),
  				format_date( $history->update_time, '', 'l j F Y - H:i:s')
  			];
  			$id++;
  		}

		
        
      

/*
        $dataInfo[] = array(
          'nbHistory' => count($histories),
          'nodeTitle' => $nodeTitle
         );*/


      /*  kint(count($histories));
        die();*/

        /*if ($histories = 0) {
        	'#markup' => $this->t("Ce contenu n'a subit aucune modification !")

        }*/

    


  		$output[] = array(
		  '#theme' => 'table',
		  //'#cache' => ['disabled' => TRUE],
		  '#caption' => $nodeTitle,
		  '#header' => $header,
		  '#rows' => $dataTable,
		);



		return array($info,$output);
		/*
				$header = ['#','Name', 'Mail'];
		$data = [
		  [1,'Name 1', 'Mail1@example.com'],
		  [2,'Name N°2', 'second@example.com'],
		];

		$output[] = array(
		  '#theme' => 'table',
		  //'#cache' => ['disabled' => TRUE],
		  '#caption' => 'The table caption / Title',
		  '#header' => $header,
		  '#rows' => $data,
		);*/
    }


}
