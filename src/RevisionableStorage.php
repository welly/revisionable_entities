<?php

namespace Drupal\revisionable;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\revisionable\Entity\RevisionableInterface;

/**
 * Defines the storage handler class for revisionable entities.
 *
 * This extends the base storage class, adding required special handling for
 * revisionable entities.
 *
 * @ingroup revisionable
 */
class RevisionableStorage extends SqlContentEntityStorage implements RevisionableStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(revisionableInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {revisionable_revision} WHERE id=:id ORDER BY vid',
      array(':id' => $entity->id())
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {revisionable_revision_field_data} WHERE uid = :uid ORDER BY vid',
      array(':uid' => $account->id())
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(revisionableInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {revisionable_revision_field_data} WHERE id = :id AND default_langcode = 1', array(':id' => $entity->id()))
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('revisionable_revision')
      ->fields(array('langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED))
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
