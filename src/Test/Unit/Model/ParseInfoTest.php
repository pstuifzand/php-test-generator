<?php

namespace Stuifzand\TestGenerator\Test\Unit\Model;

use Stuifzand\TestGenerator\Model\ParseInfo;

class ParseInfoTest extends \PHPUnit\Framework\TestCase
{
    public function testSetClassName()
    {
        $info = new ParseInfo();
        $info->setClassName("Test");
        $this->assertEquals("Test", $info->getClassName());
    }
}
