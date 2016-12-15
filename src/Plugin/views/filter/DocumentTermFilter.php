<?php

namespace Drupal\revisionable\Plugin\views\filter;

use Drupal\Core\Cache\UncacheableDependencyTrait;
use Drupal\search_api\Plugin\views\filter\SearchApiFilterTrait;
use Drupal\taxonomy\Plugin\views\filter\TaxonomyIndexTid;

/**
 * Defines a filter for filtering by topic.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("revisionable_term_filter")
 */
class RevisionableTermFilter extends TaxonomyIndexTid {

  use UncacheableDependencyTrait;
  use SearchApiFilterTrait;

  /**
   * {@inheritdoc}
   */
  public function query() {
    if ($value = reset($this->value)) {
      $this->getQuery()
        ->addCondition($this->field, $value);
    }
  }

}
