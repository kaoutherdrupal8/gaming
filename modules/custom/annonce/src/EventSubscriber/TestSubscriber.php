<?php

namespace Drupal\annonce\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Database\Database;
/**
 * Class TestSubscriber.
 */
class TestSubscriber implements EventSubscriberInterface {

  /**
   * Drupal\Core\Session\AccountProxy definition.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;
  /**
   * Drupal\Core\Database\Driver\mysql\Connection definition.
   *
   * @var \Drupal\Core\Database\Driver\mysql\Connection
   */
  protected $database;
  /**
   * Drupal\Core\Datetime\DateFormatter definition.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * Constructs a new TestSubscriber object.
   */
  public function __construct(RouteMatchInterface $currentRouteMatch, AccountProxy $current_user, Connection $database, DateFormatter $date_formatter) {
  
    $this->currentRouteMatch = $currentRouteMatch;
    $this->currentUser = $current_user;
    $this->database = $database;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events['kernel.request'] = ['onRequest'];

    return $events;
  }

  /**
   * This method is called whenever the kernel.request event is
   * dispatched.
   *
   * @param GetResponseEvent $event
   */
  public function onRequest(Event $event) {
    //kint($this->currentRoute);
    //kint($this->currentRouteMatch);
    //kint($this->database-> insert('annonce_history', array $options = array())->);
    $current_user = $this->currentUser->getAccountName();
    /*$schema = Database::getConnection();
    kint($schema));*/
    //kint($this->currentRouteMatch->getParameter('annonce')->id());
    drupal_set_message('Bonjour '.$current_user.' ');
    //kint($this->currentUser);

    if ($this->currentRouteMatch->getParameter('annonce')) {
       $this->database-> insert('annonce_history')->fields(array (
        'aid' => $this->currentRouteMatch->getParameter('annonce')->id(),
        //'uid' => $this->currentUser->id(),
        'uid' => $this->currentUser->getDisplayName(),
        'date' => time(),
        ))->execute();
      //drupal_set_message('Entity annonce');
    }
    
  }

}
