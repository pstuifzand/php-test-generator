<?php

namespace Stuifzand\TestGenerator\Test\Unit\Model;

use Stuifzand\TestGenerator\Model\ParseInfo;
use Stuifzand\TestGenerator\Model\Generator;

class GeneratorTest extends \PHPUnit\Framework\TestCase
{
    public function testGenerateAddGetObject()
    {
        $parseInfo = new ParseInfo();
        $parseInfo->setNamespace('\Test');
        $parseInfo->setShortClassName('Testing');
        $parseInfo->setClassName('\Test\Testing');

        $generator = new Generator();

        $output = $generator->generate($parseInfo);
        $this->assertContains('getObject', $output);
    }
}
