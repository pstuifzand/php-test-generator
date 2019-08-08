<?php

namespace Stuifzand\TestGenerator\Test\Unit\Model;

use Stuifzand\TestGenerator\Model\Parser;

class ParserTest extends \PHPUnit\Framework\TestCase
{
    /** @var Parser */
    private $parser;

    protected function setUp()
    {
        $this->parser = new Parser();
    }

    /**
     * @param string $className
     * @dataProvider dataProviderClassNames
     */
    public function testParserSimpleClass(string $className)
    {
        $classText = "<?php class {$className} {}";
        $info      = $this->parser->parse($classText);

        $this->assertEquals($className, $info->getClassName());
    }

    /**
     * @param string $className
     * @dataProvider dataProviderClassNames
     */
    public function testParserClassWithUse(string $className)
    {
        $classText = "<?php use Test\Test\Test; class {$className} {}";
        $info      = $this->parser->parse($classText);

        $this->assertEquals($className, $info->getClassName());
    }

    /**
     * @param string $namespace
     * @param string $className
     * @dataProvider dataProviderNamespacesAndClass
     */
    public function testParserClassWithNamespace(string $namespace, string $className)
    {
        $classText = "<?php namespace $namespace; class {$className} {}";
        $info      = $this->parser->parse($classText);
        $this->assertEquals('\\' . $namespace . '\\' . $className, $info->getClassName());
    }

    public function dataProviderClassNames()
    {
        return [
            ['Order'],
            ['OrderItemConverter'],
            ['Product'],
            ['Project'],
            ['Item'],
            ['OrderConverter'],
        ];
    }

    public function dataProviderNamespacesAndClass()
    {
        return [
            ['Guapa', 'Order'],
            ['Guapa\\Import2', 'OrderItemConverter'],
            ['Guapa\\Import3\\Model', 'Product'],
            ['Guapa\\Import4\\Model\\Api', 'Project'],
            ['Guapa\\Import5\\Test\\Test\\Test\\Test', 'Item'],
        ];
    }
}

