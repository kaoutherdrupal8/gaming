<?php

namespace Drupal\splash_reservation\Plugin\Block;

use Drupal\Core\Block\BlockBase;

use Drupal\paragraphs\Entity\Paragraph;

/**
 * Provides a 'ButtonGoReservationPerActivities' Block.
 *
 * @Block(
 *   id = "planning_on_activity_page_block",
 *   admin_label = @Translation("Planning on activity page"),
 *   category = @Translation("On the page of an activity, adding table of hour session for an activity"),
 * )
 */
class PlanningPerActivitiesBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {


  	$node   = \Drupal::routeMatch()->getParameter('node');

    $table = $this->getTable($node);

    $output["table"] = array(
      '#theme' => 'table',
      '#header' => $table['header'],
      '#cache' => ['disabled' => TRUE],
      '#rows' => $table['options']
    );

      /*if ( !empty($options) ) {
        $output["table"]['#caption'] = $message;
      }*/

    return array($output);

  }


  public function getTable($node){

    $nodeTitle = $node->getTitle();
    $nodeDuree = $node->get('field_duree')->value;
    $target_id = $node->get('field_planning')->target_id;

    $paragraph = Paragraph::load($target_id);

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

    $getPlanning = array();

    foreach ($traitementPlan as $day => $horaires) {
      if ( count($horaires) > 0 ) {
        for ($i=0; $i < count($horaires); $i++) { 
          $horaire = $this->convertInHoraire($horaires[$i]['value']) ;
          $uniqKey = $this->setUniqueIdForHourTable($horaire, $day);
          $getPlanning[$uniqKey] = $day;
        }
      }
    }

    $hours = $getPlanning;
    ksort( $hours);

    $newHour = array();

    foreach ($hours as $hour => $day) {

      $newKey = substr($hour, 0, strpos($hour, "_"));

      if (isset($newHour[$newKey])) {

        if ( count($newHour[$newKey]) > 1 ) {
          array_push( $newHour[$newKey] , $day );
        }
        else {
          $newHour[$newKey] = array( $newHour[$newKey] , $day );
        }
        
      }
      else {
        $newHour[$newKey] = $day;
      }

    }


    $setTable = array();
    $setTable['header'] = array(
      'lundi',
      'mardi',
      'mercredi',
      'jeudi',
      'vendredi',
      'samedi',
      'dimanche',
    );

    foreach ($newHour as $hour => $day) {

      $data = array();
      $data[] = array( 'data' => $hour, 'header' => true );

        if (count($day) > 1) {

          for ($i=0; $i < count($setTable['header']); $i++) { 
            
            $find = false;
            for ($j=0; $j < count($day); $j++) { 

              if ($setTable['header'][$i] === $day[$j]) {
                $find = true;
                $data[] = array( 
                  'data' => $nodeTitle." - ".$nodeDuree." min", 
                  'class' => "session"
                );
                break;
              }
                
            }
            if ($find === false) {
              $data[] = "";
            }

          }
            

        }
        else {

          for ($i=0; $i < count($setTable['header']); $i++) { 

            if ($setTable['header'][$i] === $day) {
              $data[] = array( 
                'data' => $nodeTitle." - ".$nodeDuree." min", 
                'class' => "session"
              );
            }else {
              $data[] = "";
            }
            
          }

        }
        
      $setTable['options'][] = $data;
    }

    array_unshift ( $setTable['header'] , "");

    return $setTable;
  }





  public function convertInHoraire($time){
    $hTime = 3600;
    $mTime = 60;
    $heures = strlen( intval( $time / $hTime )) === 1 ? "0".intval( $time / $hTime ) : intval( $time / $hTime ) ;
    $minutes = (intval( ( $time % $hTime ) / $mTime ) == 0 ? "00": intval( ( $time % $hTime ) / $mTime )  ) ;
    return $heures.":".$minutes;
  }


  public function setUniqueIdForHourTable($horaire, $day){

    switch ($day) {
      case 'lundi':
        $id = 1;
        break;
      case 'mardi':
        $id = 2;
        break;
      case 'mercredi':
        $id = 3;
        break;
      case 'jeudi':
        $id = 4;
        break;
      case 'vendredi':
        $id = 5;
        break;
      case 'samedi':
        $id = 6;
        break;
      case 'dimanche':
        $id = 7;
        break;
      
      default:
        $id = 0;
        break;
    }
    return $horaire."_".$id;
  }



}