<?php

namespace Drupal\annonce;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Annonce entity.
 *
 * @see \Drupal\annonce\Entity\Annonce.
 */
class AnnonceAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\annonce\Entity\AnnonceInterface $entity */

    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished annonce entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published annonce entities');

      case 'update':
          if ($account->id() == $entity->getOwnerId())  {
             return AccessResult::allowedIfHasPermission($account, 'edit annonce entities');
          }
            return AccessResult::forbidden();

      case 'delete':
         if ($account->id() == $entity->getOwnerId())  {
             return AccessResult::allowedIfHasPermission($account, 'delete annonce entities');
          }

           return  AccessResult::forbidden();
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  //

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add annonce entities');
  }

}
