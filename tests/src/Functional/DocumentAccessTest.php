<?php

namespace Drupal\Tests\revisionable\Functional;

use Drupal\council\Entity\Council;
use Drupal\revisionable\Entity\Revisionable;
use Drupal\Tests\lgt_profile\Functional\LgToolboxTestBase;

/**
 * Tests revisionable entity access controls.
 *
 * @group revisionable
 */
class RevisionableAccessTest extends LgToolboxTestBase {

  /**
   * Tests access controls.
   */
  public function testCouncilEditAccess() {

    // Create a council.
    $council = Council::create([
      'id' => 'WaggaWagga',
      'label' => 'Wagga Wagga',
    ]);
    $council->save();
    $this->cleanupEntities[] = $council;

    // Create an normal user.
    $authenticatedUser = $this->drupalCreateUser();

    $permissions = [
      'access revisionable entities overview',
      'delete revisionable entities',
      'edit revisionable entities',
    ];

    // Create an admin user.
    $adminUser = $this->drupalCreateUser($permissions);

    // Create a council editor user.
    $councilEditorUser = $this->drupalCreateUser($permissions);
    $councilEditorUser->council = $council;
    $councilEditorUser->save();

    // Create a revisionable.
    $revisionable = Revisionable::create([
      'label' => 'Test revisionable',
      'council' => $council,
      'user_id' => $adminUser,
    ]);
    $revisionable->save();
    $this->cleanupEntities[] = $revisionable;

    $this->drupalLogin($adminUser);
    $session = $this->assertSession();

    $this->drupalGet('admin/structure/revisionable');
    $session->statusCodeEquals(200);
    $session->linkByHrefExists($revisionable->toUrl('edit-form')->toString());
    $this->drupalLogout();

    $this->drupalLogin($councilEditorUser);
    $this->drupalGet($revisionable->toUrl('edit-form'));
    $session->statusCodeEquals(200);
    $session->linkByHrefExists($revisionable->toUrl('edit-form')->toString());
    $this->drupalLogout();

  }

}
