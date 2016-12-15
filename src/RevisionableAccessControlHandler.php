<?php

namespace Drupal\revisionable;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Revisionable entity.
 *
 * @see \Drupal\revisionable\Entity\Revisionable.
 */
class RevisionableAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\revisionable\RevisionableInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished revisionable entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published revisionable entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit revisionable entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete revisionable entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add revisionable entities');
  }

}
