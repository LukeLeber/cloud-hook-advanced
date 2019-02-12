<?php

namespace lleber\Test\Acquia\Cloudapi;

use lleber\Acquia\Cloudapi\V1;
use lleber\Acquia\Cloudapi\V1\Database;
use lleber\Acquia\Cloudapi\V1\Varnish;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class V1Test.
 *
 * Contains test cases for the \lleber\Acquia\Cloudapi\V1 class.
 *
 * @package lleber\Test\Acquia\Cloudapi
 */
class V1Test extends TestCase {

  /**
   * An instance of the subject under test.
   *
   * @var \lleber\Acquia\Cloudapi\V1
   */
  protected $v1;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    /* @var $database \lleber\Acquia\Cloudapi\V1\Database|\PHPUnit\Framework\MockObject\MockObject */
    $database = $this->getMockBuilder(Database::class)
      ->setMethods(['backup'])
      ->getMock();

    /* @var $varnish \lleber\Acquia\Cloudapi\V1\Varnish|\PHPUnit\Framework\MockObject\MockObject */
    $varnish = $this->getMockBuilder(Varnish::class)
      ->setMethods(['clear'])
      ->getMock();

    $this->v1 = new V1($database, $varnish);
  }

  /**
   * @covers \lleber\Acquia\Cloudapi\V1::__construct
   * @covers \lleber\Acquia\Cloudapi\V1::getVarnish
   * @covers \lleber\Acquia\Cloudapi\V1::clearVarnish
   */
  public function testClearVarnish() {

    /* @var $varnish_mock \lleber\Acquia\Cloudapi\V1\Varnish|\PHPUnit\Framework\MockObject\MockObject */
    $varnish_mock = $this->v1->getVarnish();

    $this->assertInstanceOf(MockObject::class, $varnish_mock);

    $varnish_mock
      ->expects($this->once())
      ->method('clear');

    $this->v1->clearVarnish('test', 'dev');
  }

  /**
   * @covers \lleber\Acquia\Cloudapi\V1::__construct
   * @covers \lleber\Acquia\Cloudapi\V1::getDatabase
   * @covers \lleber\Acquia\Cloudapi\V1::backupDatabases
   */
  public function testBackupDatabases() {

    /* @var $database_mock \lleber\Acquia\Cloudapi\V1\Database|\PHPUnit\Framework\MockObject\MockObject */
    $database_mock = $this->v1->getDatabase();

    $this->assertInstanceOf(MockObject::class, $database_mock);

    $database_mock->expects($this->once())
      ->method('backup');

    $this->v1->backupDatabases('test', 'dev');
  }
}
