<?php

namespace Drupal\revisionable\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\revisionable\Entity\RevisionableInterface;

/**
 * Class RevisionableController.
 *
 *  Returns responses for Revisionable routes.
 *
 * @package Drupal\revisionable\Controller
 */
class RevisionableController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Revisionable revision.
   *
   * @param int $revisionable_revision
   *   The Revisionable revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($revisionable_revision) {
    $revisionable = $this->entityManager()->getStorage('revisionable')->loadRevision($revisionable_revision);
    $view_builder = $this->entityManager()->getViewBuilder('revisionable');
    return $view_builder->view($revisionable);
  }

  /**
   * Page title callback for a revisionable revision.
   *
   * @param int $revisionable_revision
   *   The revisionable  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($revisionable_revision) {
    $revisionable = $this->entityManager()->getStorage('revisionable')->loadRevision($revisionable_revision);
    return $this->t('Revision of %title from %date', array('%title' => $revisionable->label(), '%date' => format_date($revisionable->getRevisionCreationTime())));
  }

  /**
   * Generates an overview table of older revisions of a revisionable .
   *
   * @param \Drupal\revisionable\Entity\revisionableInterface $revisionable
   *   A revisionable  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(revisionableInterface $revisionable) {
    $account = $this->currentUser();
    $langcode = $revisionable->language()->getId();
    $langname = $revisionable->language()->getName();
    $languages = $revisionable->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $revisionable_storage = $this->entityManager()->getStorage('revisionable');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $revisionable->label()]) : $this->t('Revisions for %title', ['%title' => $revisionable->label()]);
    $header = array($this->t('Revision'), $this->t('Operations'));

    $revert_permission = (($account->hasPermission("revert all revisionable revisions") || $account->hasPermission('administer revisionable entities')));
    $delete_permission = (($account->hasPermission("delete all revisionable revisions") || $account->hasPermission('administer revisionable entities')));

    $rows = array();
    $vids = $revisionable_storage->revisionIds($revisionable);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\revisionable\Entity\RevisionableInterface $revision */
      $revision = $revisionable_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      $a = $revision->hasTranslation($langcode);
      $b = $revision->getTranslation($langcode)->isRevisionTranslationAffected();

      if ($revision->hasTranslation($langcode)) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionAuthor(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->revision_timestamp->value, 'short');
        if ($vid != $revisionable->getRevisionId()) {
          $link = $this->l($date, new Url('entity.revisionable.revision', ['revisionable' => $revisionable->id(), 'revisionable_revision' => $vid]));
        }
        else {
          $link = $revisionable->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{{ date }} by {{ username }}<p class="revision-log">{{ message }}</p>',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->revision_log_message->value, '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => Url::fromRoute('revisionable.revision_revert_confirm', ['revisionable' => $revisionable->id(), 'revisionable_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('revisionable.revision_delete_confirm', [
                'revisionable' => $revisionable->id(),
                'revisionable_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['revisionable_revisions_table'] = array(
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    );
    return $build;
  }

}
