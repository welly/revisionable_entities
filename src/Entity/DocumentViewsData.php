<?php

namespace Drupal\revisionable\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Revisionable entities.
 */
class RevisionableViewsData extends EntityViewsData implements EntityViewsDataInterface {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['revisionable']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Revisionable'),
      'help' => $this->t('The Revisionable ID.'),
    );

    return $data;
  }

}
