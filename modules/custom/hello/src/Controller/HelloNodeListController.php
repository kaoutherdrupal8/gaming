<?php

namespace Drupal\hello\Controller;

use Drupal\Core\Controller\ControllerBase;


class HelloNodeListController extends ControllerBase{

  public function content($nodetype){
    //$query = \Drupal::entityQuery('node');
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    //kint($storage);
    //die();

    $ids = \Drupal::entityQuery('node')->pager('10')->execute();
    //kint($ids);
    //die();
    if ($nodetype !== "no parameter") {
      $ids = \Drupal::entityQuery('node')->condition('type', $nodetype)->pager('10')->execute();
      //kint(  $ids);
    }
  //  $nids = $query->pager('10')->execute();
    $entities = $storage->loadMultiple($ids);
    //kint($entities);
    //die();
  $items = array();
  foreach($entities as $entity)
      {
        $items[] = $entity->toLink();
    //        kint($entity->getTitle());
    //        die();
       }


    // $items = ['pim', 'pam', 'poum'];
    $list =  array(
      '#theme' => 'item_list',
      '#items' => $items,
    );

    $pager =  array(
      '#type' => 'pager'
    );

    return array($list, $pager);

  }

}
