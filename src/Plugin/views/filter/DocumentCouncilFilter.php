<?php

namespace Drupal\revisionable\Plugin\views\filter;

use Drupal\Core\Cache\UncacheableDependencyTrait;
use Drupal\Core\Form\FormStateInterface;
use Drupal\council\Entity\Council;
use Drupal\council\Entity\CouncilInterface;
use Drupal\search_api\Plugin\views\filter\SearchApiFilterTrait;
use Drupal\views\Plugin\views\filter\StringFilter;

/**
 * Council filter.
 *
 * @ViewsFilter("revisionable_council_filter")
 */
class RevisionableCouncilFilter extends StringFilter {

  use UncacheableDependencyTrait;
  use SearchApiFilterTrait;

  /**
   * {@inheritdoc}
   */
  public function buildExposedForm(&$form, FormStateInterface $form_state) {
    parent::buildExposedForm($form, $form_state);
    $value = $this->options['expose']['identifier'];
    $form[$value]['#type'] = 'select';
    $form[$value]['#options'] = array_map(function (CouncilInterface $council) {
      return $council->label();
    }, Council::loadMultiple());
    $form[$value]['#size'] = 1;
    $form[$value]['#empty_option'] = '- Any -';
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    if ($value = $this->value) {
      $this->getQuery()
        ->addCondition($this->field, $value);
    }
  }

}
