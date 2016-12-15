<?php

namespace Drupal\revisionable;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface RevisionableStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of revisionable revision IDs for a specific revisionable.
   *
   * @param \Drupal\revisionable\Entity\RevisionableInterface $entity
   *   The revisionable entity.
   *
   * @return int[]
   *   revisionable revision IDs (in ascending order).
   */
  public function revisionIds(RevisionableInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as revisionable author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   revisionable revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\revisionable\Entity\RevisionableInterface $entity
   *   The revisionable entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(RevisionableInterface $entity);

  /**
   * Unsets the language for all revisionable with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
