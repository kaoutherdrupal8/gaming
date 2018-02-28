<?php

namespace Drupal\hello\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Hello' Block.
 *
 * @Block(
 *   id = "hello_block",
 *   admin_label = @Translation("Hello block"),
 *   category = @Translation("Hello World"),
 * )
 */
class HelloBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $timestamp = \Drupal::time()->getCurrentTime();
    $currentTimeFormater = format_date($timestamp, 'custom','G\hi s\s');
    $userCurrent = \Drupal::currentUser()->getAccountName();

    $message = $this->t("Welcome in our website %user . Tt's %currentTime",
      array(
        "%currentTime"=>$currentTimeFormater,
        "%user" => $userCurrent
      )
    );

    return array(
      '#markup' => $message,
      '#cache' => [
            'max-age' => '10'
        ]
    );


  }
}
