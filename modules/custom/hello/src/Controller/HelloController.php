<?php

namespace Drupal\hello\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class HelloController extends ControllerBase{

  public function content($param){
    //$service = \Drupal::service;
    //kint($this->currentUser());
    //kint(\Drupal::currentUser());
    //kint(\Drupal::service('current_user'));

    $message = $this->t('You are on the hello page. Your name is %username!.',
         array(
         '%username' => $this->currentUser()->getAccountName()
          )
        );

      if ($param !== "no parameter" ) {
        $message2 = $this->t('Your param is %param',
             array(
             '%param' => $param
              ));
        $message = $message . "<br>". $message2;
      }

    $build = array('#markup' => $message);
        return $build;

  }


  public function testResponse(){
   //Solution 1
    $toto = array(
      'Auj' => "trés Froid" ,
      'Demain' => "Froid"
    );

    $response = new Response(
        json_encode($toto),
      Response::HTTP_OK,
      array('content-type' => 'application/json')
  );

        return $response;
  //solution2:
  return new JsonResponse(array('do', 're', 'mi'));
  }

}
