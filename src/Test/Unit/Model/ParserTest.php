<?php

namespace Stuifzand\TestGenerator\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Stuifzand\TestGenerator\Model\ParseInfo;
use Stuifzand\TestGenerator\Model\Parser;

class ParserTest extends TestCase
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

        $this->assertFalse($info->hasConstructor());
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

        $this->assertFalse($info->hasConstructor());
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
        $this->assertFalse($info->hasConstructor());
        $this->assertEquals('\\' . $namespace . '\\' . $className, $info->getClassName());
    }

    public function testParserClassEmptyConstructor()
    {
        $classText = <<<PHP
<?php namespace Test;
class Parser
{
    public function __construct() {}
}
PHP;

        /** @var ParseInfo $info */
        $info = $this->parser->parse($classText);

        $this->assertEquals('\\Test\\Parser', $info->getClassName());
        $this->assertTrue($info->hasConstructor());
        $this->assertEquals([], $info->getConstructorArguments());
    }

    public function testParserPHPDoc()
    {
        $classText = <<<PHP
<?php namespace Test;
class Parser
{
    /**
     */
    public function __construct() {}
}
PHP;

        /** @var ParseInfo $info */
        $info = $this->parser->parse($classText);

        $this->assertEquals('\\Test\\Parser', $info->getClassName());
        $this->assertTrue($info->hasConstructor());
        $this->assertEquals([], $info->getConstructorArguments());
    }

    public function testParserPHPDocTwoMethods()
    {
        $classText = <<<PHP
<?php namespace Test;
class Parser
{
    /**
     */
    public function __construct() {}

    /**
     */
    public function method1() {}
}
PHP;

        /** @var ParseInfo $info */
        $info = $this->parser->parse($classText);

        $this->assertEquals('\\Test\\Parser', $info->getClassName());
        $this->assertTrue($info->hasConstructor());
        $this->assertEquals([], $info->getConstructorArguments());
    }

    public function testParserPHPDocTwoMethodsSwitched()
    {
        $classText = <<<PHP
<?php namespace Test;
class Parser
{
    /**
     */
    public function method1() {}

    /**
     */
    public function __construct() {}
}
PHP;

        /** @var ParseInfo $info */
        $info = $this->parser->parse($classText);

        $this->assertEquals('\\Test\\Parser', $info->getClassName());
        $this->assertTrue($info->hasConstructor());
        $this->assertEquals([], $info->getConstructorArguments());
    }

    public function testParserPHPDocTwoMethodsOneParam()
    {
        $classText = <<<PHP
<?php namespace Test;
class Parser
{
    /**
     * @param \Magento\Sales\Model\Order \$order
     */
    public function __construct(\$order) {}

    /**
     */
    public function method1() {}
}
PHP;

        /** @var ParseInfo $info */
        $info = $this->parser->parse($classText);

        $this->assertEquals('\\Test\\Parser', $info->getClassName());
        $this->assertTrue($info->hasConstructor());
        $this->assertEquals([
            ['\\Magento\\Sales\\Model\\Order', 'order'],
        ], $info->getConstructorArguments());
    }

    public function testParserPHPDocTwoMethodsTwoParams()
    {
        $classText = <<<PHP
<?php namespace Test;
class Parser
{
    /**
     * @param \Magento\Sales\Model\OrderFactory \$orderFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface \$orderRepository
     */
    public function __construct(\$orderFactory, \$repo) {}

    /**
     */
    public function method1() {}
}
PHP;

        /** @var ParseInfo $info */
        $info = $this->parser->parse($classText);
        $this->assertEquals('\\Test', $info->getNamespace());
        $this->assertEquals('Parser', $info->getShortClassName());
        $this->assertEquals('\\Test\\Parser', $info->getClassName());
        $this->assertTrue($info->hasConstructor());
        $this->assertEquals([
            ['\\Magento\\Sales\\Model\\OrderFactory', 'orderFactory'],
            ['\\Magento\\Sales\\Api\\OrderRepositoryInterface', 'orderRepository'],
        ], $info->getConstructorArguments());
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
            ['Stuifzand', 'Order'],
            ['Stuifzand\\Import2', 'OrderItemConverter'],
            ['Stuifzand\\Import3\\Model', 'Product'],
            ['Stuifzand\\Import4\\Model\\Api', 'Project'],
            ['Stuifzand\\Import5\\Test\\Test\\Test\\Test', 'Item'],
        ];
    }
}
