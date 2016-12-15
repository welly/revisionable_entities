<?php

namespace Drupal\revisionable\Entity;

use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Revisionable entities.
 *
 * @ingroup revisionable
 */
interface RevisionableInterface extends RevisionableInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Revisionable name.
   *
   * @return string
   *   Name of the Revisionable.
   */
  public function getName();

  /**
   * Sets the Revisionable name.
   *
   * @param string $name
   *   The Revisionable name.
   *
   * @return \Drupal\revisionable\Entity\RevisionableInterface
   *   The called Revisionable entity.
   */
  public function setName($name);

  /**
   * Gets the Revisionable creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Revisionable.
   */
  public function getCreatedTime();

  /**
   * Sets the Revisionable creation timestamp.
   *
   * @param int $timestamp
   *   The Revisionable creation timestamp.
   *
   * @return \Drupal\revisionable\Entity\RevisionableInterface
   *   The called Revisionable entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Revisionable published status indicator.
   *
   * Unpublished Revisionable are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Revisionable is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Revisionable.
   *
   * @param bool $published
   *   TRUE to set this Revisionable to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\revisionable\Entity\RevisionableInterface
   *   The called Revisionable entity.
   */
  public function setPublished($published);

  /**
   * Gets the revisionable revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the revisionable revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\revisionable\Entity\RevisionableInterface
   *   The called revisionable entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the revisionable revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionAuthor();

  /**
   * Sets the revisionable revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\revisionable\Entity\RevisionableInterface
   *   The called revisionable entity.
   */
  public function setRevisionAuthorId($uid);

}
