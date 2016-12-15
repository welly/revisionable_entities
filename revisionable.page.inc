<?php

/**
 * @file
 * Contains revisionable.page.inc.
 *
 * Page callback for Revisionable entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Revisionable templates.
 *
 * Default template: revisionable.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_revisionable(array &$variables) {
  // Fetch Revisionable Entity Object.
  $revisionable = $variables['elements']['#revisionable'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
