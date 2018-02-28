<?php

namespace Drupal\annonce\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Annonce entities.
 */
class AnnonceViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
    /*kint($data);
    die();*/

   $data['annonce_history']['table']['group'] = t('Annonce history');
   $data['annonce_history']['table']['provider'] = 'annonce';
   $data['annonce_history']['table']['base'] = array(
     'fields' => 'ahid',
     'title' => t('annonce_history'),
     'help' => t('Annonce history contains historical datas and can be related to annonces.'),
     'weight' => -100,
   );
   $data['annonce_history']['uid'] = array(
     'title' => t(' User ID'),
     'help'=> t('Annonce User ID.'),
     'field' => array('id' =>'numeric'),
     'sort' => array('id' => 'numeric'),
     'filter' => array('id' => 'numeric'),
     'relationship' => array(
        'base' => 'users_field_data',
        'base field' => 'uid',
        'id' => 'standard',
        'label' => t('Annonce history UID -> User ID'),
      ),
     );

   /*$data['annonce_history']['uid'] = array(
     'title' => t('Name User '),
     'help'=> t('Name User .'),
     'field' => array('id' =>'text'),
     'sort' => array('id' => 'text'),
     'filter' => array('id' => 'text'),
     'relationship' => array(
        'base' => 'users_field_data',
        'base field' => 'uid',
        'id' => 'standard',
        'label' => t('Annonce history UID -> User ID'),
      ),
     );*/
    // Additional information for Views integration, such as table joins, can be
    // put here.
   $data['annonce_history']['date'] = array(
     'title' => t('date'),
     'help'=> t('date'),
     'field' => array('id' =>'date'),
     'sort' => array('id' => 'date'),
     'filter' => array('id' => 'date'),

     );

   $data['annonce_history']['aid'] = array(
     'title' => t('desc Annonce  ID'),
     'help'=> t('Annonce ID.'),
     'field' => array('id' =>'numeric'),
     'sort' => array('id' => 'numeric'),
     'filter' => array('id' => 'numeric'),
     'relationship' => array(
        'base' => 'annonce_field_data',
        'base field' => 'id',
        'id' => 'standard',
        'label' => t('Annonce history AID -> Annonce ID'),
      ),
     );
    return $data;
  }

}
