<?php

namespace Drupal\Tests\revisionable\Functional;

use Drupal\council\Entity\Council;
use Drupal\Tests\lgt_profile\Functional\LgToolboxTestBase;

/**
 * Tests revisionable entity creation.
 *
 * @group revisionable
 */
class RevisionableCreateTest extends LgToolboxTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Testing ability to create revisionable entities.
   */
  public function testRevisionableCreation() {

    // Create a council.
    $council = Council::create([
      'id' => 'Bundaberg',
      'label' => 'Bundaberg',
    ]);
    $council->save();
    $this->cleanupEntities[] = $council;

    $adminUser = $this->createAdminUser();
    $this->drupalLogin($adminUser);

    $this->drupalGet('admin/structure/revisionable');
    $session = $this->assertSession();
    $session->statusCodeEquals(200);
  }

}
