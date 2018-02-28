<?php

namespace Drupal\hello\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\Query\SelectInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
/**
 * Provides a 'Hello' Block.
 *
 * @Block(
 *   id = "session_active",
 *   admin_label = @Translation("session block"),
 *   category = @Translation("Hello World"),
 * )
 */
class SessionActiveBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    //
    // $message = $this->t("Welcome in our website %user . Tt's %currentTime",
    //   array(
    //     "%currentTime"=>$currentTimeFormater,
    //     "%user" => $userCurrent
    //   )
    // );
  $database = \Drupal::database();
  $session = $database->select('sessions');
  //$session2 = $database->select('users_field_data');
  //$connexion2 = $session2
  $connexion = $session->countQuery()->execute()->fetchField();
  $userCurrent = \Drupal::currentUser()->getUsername();
  //kint($session2);
  //die();
  //kint(\Drupal::currentuser());
  //die();
   //kint($session->countQuery()->execute()->fetchField());
   //die();
    //$message = "hello word";
  //$session = \Drupal::database::countQuery()->execute()->fetchField();

  $message = $this->t("Bonjour %user il y a %sessionActive sessions actives .",
    array(
      "%sessionActive"=>$connexion,
      "%user"=>$userCurrent

    )
  );


    return array(
      '#markup' => $message,
      '#cache' => [
            'max-age' => '10'
        ]
    );


  }

  protected function blockAccess(AccountInterface $account){

    return AccessResult::allowedIfHasPermission($account,'access hello');
  }
}
