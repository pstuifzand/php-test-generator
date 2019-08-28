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

    public function testGenerateShouldHaveFourSpaceIndents()
    {
        $parseInfo = new ParseInfo();
        $parseInfo->setNamespace('\Test');
        $parseInfo->setShortClassName('Testing');
        $parseInfo->setClassName('\Test\Testing');
        $parseInfo->setConstructor(true);
        $parseInfo->setConstructorArguments([
            ['\\Magento\\Sales\\Model\\OrderFactory', 'orderFactory'],
            ['\\Magento\\Sales\\Api\\OrderRepositoryInterface', 'orderRepository'],
        ]);
        $generator = new Generator();
        $output = $generator->generate($parseInfo);
        preg_match_all('#^( {1,})#sm', $output, $matches);

        foreach ($matches as $submatches) {
            foreach ($submatches as $indent) {
                $this->assertEquals(
                    0,
                    strlen($indent) % 4,
                    'Indent length == '.strlen($indent) . ', should be multiple of 4'
                );
            }
        }
    }
}
