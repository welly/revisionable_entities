<?php

namespace Drupal\revisionable\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Revisionable revision.
 *
 * @ingroup revisionable
 */
class RevisionableDeleteRevisionForm extends ConfirmFormBase {

  /**
   * The revisionable revision.
   *
   * @var \Drupal\revisionable\Entity\RevisionableInterface
   */
  protected $revision;

  /**
   * The revisionable storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $revisionableStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs a new RevisionableRevisionDeleteForm.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   *   The entity storage.
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(EntityStorageInterface $entity_storage, Connection $connection) {
    $this->revisionableStorage = $entity_storage;
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $entity_manager = $container->get('entity.manager');
    return new static(
      $entity_manager->getStorage('revisionable'),
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'revisionable_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Are you sure you want to delete the revision from %revision-date?', array('%revision-date' => format_date($this->revision->getRevisionCreationTime())));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.revisionable.version_history', array('revisionable' => $this->revision->id()));
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $revisionable_revision = NULL) {
    $this->revision = $this->revisionableStorage->loadRevision($revisionable_revision);
    $form = parent::buildForm($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->revisionableStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')
      ->notice('revisionable: deleted %title revision %revision.', array(
        '%title' => $this->revision->label(),
        '%revision' => $this->revision->getRevisionId(),
      ));

    drupal_set_message(t('Revision from %revision-date of revisionable %title has been deleted.', array(
      '%revision-date' => format_date($this->revision->getRevisionCreationTime()),
      '%title' => $this->revision->label(),
    )));

    $form_state->setRedirect(
      'entity.revisionable.canonical',
      array('revisionable' => $this->revision->id())
    );

  }

}
