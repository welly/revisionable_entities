<?php

namespace Drupal\Tests\revisionable\Kernel;

use Drupal\council\Entity\Council;
use Drupal\revisionable\Entity\Revisionable;
use Drupal\KernelTests\KernelTestBase;
use Drupal\simpletest\UserCreationTrait;

/**
 * Tests revisionable access.
 *
 * @group revisionable
 *
 * @covers \Drupal\revisionable\RevisionableAccessControlHandler
 */
class RevisionableAccessTest extends KernelTestBase {

  use UserCreationTrait;

  /**
   * Test council.
   *
   * @var \Drupal\council\Entity\CouncilInterface
   */
  protected $council;

  /**
   * Test revisionable.
   *
   * @var \Drupal\revisionable\Entity\RevisionableInterface
   */
  protected $revisionable;

  /**
   * Test user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * Test user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $councilEditorUser;

  /**
   * Test user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $authenticatedUser;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'council',
    'revisionable',
    'file',
    'link',
    'system',
    'taxonomy',
    'text',
    'user',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->installSchema('system', ['sequences']);
    $this->installEntitySchema('revisionable');
    $this->installEntitySchema('user');

    // Create a council.
    $this->council = Council::create([
      'id' => 'Oceania',
      'label' => 'Oceania',
    ]);
    $this->council->save();

    // Create an admin user.
    $this->adminUser = $this->createUser([], 'Emmanuel Goldstein', TRUE);

    // Create an normal user.
    $this->authenticatedUser = $this->createUser([], 'Tom Parsons');

    $permissions = [
      'access revisionable entities overview',
      'delete revisionable entities',
      'edit revisionable entities',
    ];

    // Create a council editor user.
    $this->councilEditorUser = $this->createUser($permissions, 'Winston Smith');
    $this->councilEditorUser->council = $this->council;
    $this->councilEditorUser->save();

    // Create a revisionable.
    $this->revisionable = Revisionable::create([
      'label' => 'Test revisionable',
      'council' => $this->council,
      'user_id' => $this->adminUser,
    ]);
    $this->revisionable->save();
  }

  /**
   * Tests ability to edit and delete revisionables.
   */
  public function testRevisionableEditAndDeleteAccess() {

    // Council editor user can edit revisionable content.
    $this->assertTrue($this->revisionable->access('update', $this->councilEditorUser));

    // Council editor user can edit own content.
    $this->assertTrue($this->revisionable->access('delete', $this->councilEditorUser));

    // Authenticated cannot.
    $this->assertFalse($this->revisionable->access('update', $this->authenticatedUser));
  }

}
