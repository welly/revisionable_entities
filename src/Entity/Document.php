<?php

namespace Drupal\revisionable\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\link\LinkItemInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Revisionable entity.
 *
 * @ingroup revisionable
 *
 * @ContentEntityType(
 *   id = "revisionable",
 *   label = @Translation("Revisionable"),
 *   handlers = {
 *     "storage" = "Drupal\revisionable\RevisionableStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\revisionable\RevisionableListBuilder",
 *     "views_data" = "Drupal\revisionable\Entity\RevisionableViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\revisionable\Form\RevisionableForm",
 *       "add" = "Drupal\revisionable\Form\RevisionableForm",
 *       "edit" = "Drupal\revisionable\Form\RevisionableForm",
 *       "delete" = "Drupal\revisionable\Form\RevisionableDeleteForm",
 *     },
 *     "access" = "Drupal\revisionable\RevisionableAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\revisionable\RevisionableHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "revisionable",
 *   admin_permission = "administer revisionable entities",
 *   data_table = "revisionable_field_data",
 *   revision_table = "revisionable_revision",
 *   revision_data_table = "revisionable_revision_field_data",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/content/revisionable/{revisionable}",
 *     "add-form" = "/admin/content/revisionable/add",
 *     "edit-form" = "/admin/content/revisionable/{revisionable}/edit",
 *     "delete-form" = "/admin/content/revisionable/{revisionable}/delete",
 *     "version-history" = "/admin/content/revisionable/{revisionable}/revisions",
 *     "revision" = "/admin/content/revisionable/{revisionable}/revisions/{revisionable_revision}/view",
 *     "revision_delete" = "/admin/content/revisionable/{revisionable}/revisions/{revisionable_revision}/delete",
 *     "revision_revert" = "/admin/content/revisionable/{revisionable}/revisions/{revisionable_revision}/revert",
 *     "collection" = "/admin/structure/revisionable",
 *   },
 *   field_ui_base_route = "entity.revisionable.collection"
 * )
 */
class Revisionable extends RevisionableContentEntityBase implements RevisionableInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the Revisionable owner
    // the revision author.
    if (!$this->getRevisionAuthor()) {
      $this->setRevisionAuthorId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return $this->get('file')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setFile($file) {
    $this->set('file', $file);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLink() {
    return $this->get('link')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setLink($link) {
    $this->set('link', $link);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionCreationTime() {
    return $this->get('revision_timestamp')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionCreationTime($timestamp) {
    $this->set('revision_timestamp', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionAuthor() {
    return $this->get('revision_uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionAuthorId($uid) {
    $this->set('revision_uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Revisionable entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 10,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 10,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ));

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Revisionable entity.'))
      ->setRevisionable(TRUE)
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -10,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -10,
      ));

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_default',
        'weight' => -8,
      ])
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => -8,
      ]);

    $extensions = 'csv doc docx pdf ppt pptx rdf txt xls xlsx';
    $fields['file'] = BaseFieldDefinition::create('file')
      ->setLabel(t('File'))
      ->setRevisionable(TRUE)
      ->setDefaultValue('')
      ->setSetting('file_extensions', $extensions)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'file',
        'weight' => -6,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'file',
        'weight' => -6,
      ));

    $fields['link'] = BaseFieldDefinition::create('link')
      ->setLabel(t('Link'))
      ->setRevisionable(TRUE)
      ->setDefaultValue('')
      ->setSettings(array(
        'link_type' => LinkItemInterface::LINK_GENERIC,
        'title' => DRUPAL_DISABLED,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'link_default',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'link_default',
        'weight' => -4,
      ));

    $fields['topic'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Topic'))
      ->setDescription(t('Start typing the name of a topic to select it.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'taxonomy_term')
      ->setSetting('handler_settings',
        ['target_bundles' => ['taxonomy_term' => 'topics']])
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'taxonomy_term',
        'weight' => -2,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
        'weight' => -2,
      ));

    $fields['revisionable_type'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Revisionable type'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'taxonomy_term')
      ->setSetting('handler_settings',
        ['target_bundles' => ['taxonomy_term' => 'revisionable_type']])
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'taxonomy_term',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => 0,
      ));

    $fields['legislation'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Legislation'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'taxonomy_term')
      ->setSetting('handler_settings',
        ['target_bundles' => ['taxonomy_term' => 'legislation']])
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'taxonomy_term',
        'weight' => 2,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => 2,
      ));

    $fields['purpose'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Purpose'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'taxonomy_term')
      ->setSetting('handler_settings',
        ['target_bundles' => ['taxonomy_term' => 'purpose']])
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'taxonomy_term',
        'weight' => 4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => 4,
      ));

    $fields['council'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Council'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'council')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'council',
        'weight' => 6,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => 8,
      ));

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Revisionable is published.'))
      ->setDefaultValue(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_timestamp'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Revision timestamp'))
      ->setDescription(t('The time that the current revision was created.'))
      ->setQueryable(FALSE)
      ->setRevisionable(TRUE);

    $fields['revision_uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Revision user ID'))
      ->setDescription(t('The user ID of the author of the current revision.'))
      ->setSetting('target_type', 'user')
      ->setQueryable(FALSE)
      ->setRevisionable(TRUE);

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

}
