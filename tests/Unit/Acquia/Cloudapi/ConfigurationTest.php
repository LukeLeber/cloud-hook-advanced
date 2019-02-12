<?php

namespace lleber\Test\Acquia\Cloudapi;

use lleber\Acquia\Cloudapi\Configuration;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ConfigurationTest extends TestCase {

  /**
   * The virtual filesystem for this test.
   *
   * @var \org\bovigo\vfs\vfsStreamDirectory
   */
  protected $vfsStream;

  protected function setUp() {
    $directory_structure = [
      '.acquia' => [
        // 'missing.conf' => '{}', - see what I did there?
        'invalid.conf' => '{}',
        'cloudapi.conf' => '{}',
      ],
    ];

    $this->vfsStream = vfsStream::setup('root', 444, $directory_structure);
  }

//  public function testMissingConfig() {
//
//    /* @var $configuration_mock \lleber\Acquia\Cloudapi\Configuration|\PHPUnit\Framework\MockObject\MockObject */
//    $configuration_mock = $this->getMockBuilder(Configuration::class)
//      ->disableOriginalConstructor()
//      ->setMethods(['getConfigFilePath'])
//      ->getMock();
//
//    $configuration_mock
//      ->method('getConfigFilePath')
//      ->willReturn($this->vfsStream->url() . '/.acquia/missing.conf');
//
//    $this->expectException(\Exception::class);
//    $this->expectExceptionMessage('CloudAPI configuration could not be read from "/.acquia/missing.conf".');
//  }

}