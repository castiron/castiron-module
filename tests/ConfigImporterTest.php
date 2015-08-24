<?php

namespace Castiron\Tests;

use System\Traits\ConfigMaker;

class ConfigImporterTest extends BaseTestCase
{
    use ConfigMaker;

    /** @var \stdClass */
    protected $conf = null;

    public function setUp()
    {
        parent::setUp();
        $this->conf = $this->makeConfig('~/modules/castiron/tests/samples/start.yaml');
    }

    public function testItImportsValues()
    {
        $this->assertTrue(isset($this->conf->animals));
        $this->assertCount(3, $this->conf->animals);
    }

    public function testOverridingImportedValues()
    {
        $this->assertTrue(isset($this->conf->colors));
        $this->assertCount(2, $this->conf->colors);
        $this->assertEquals('orange', $this->conf->colors[1]);
    }

    public function testUnimportedValuesRemain()
    {
        $this->assertTrue(isset($this->conf->foods));
        $this->assertCount(3, $this->conf->foods['dairy']);
    }

    public function testDeepImportedValues()
    {
        $this->assertCount(2, $this->conf->foods['meats']);
        $this->assertTrue(isset($this->conf->foods['meats']['pork']));
        $this->assertCount(3, $this->conf->foods['meats']['fish']);
    }

}
