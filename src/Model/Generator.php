<?php

namespace Stuifzand\TestGenerator\Model;

class Generator
{
    /**
     * @param ParseInfo $info
     * @return string
     */
    public function generate(ParseInfo $info): string
    {
        ob_start();
        echo "<?php\n\n";

        $testNamespace = explode('\\', $info->getNamespace());
        array_splice($testNamespace, 3, 0, ['Test', 'Unit']);
        array_shift($testNamespace);
        echo "namespace " . implode('\\', $testNamespace) . ";\n\n";

        echo "use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;\n\n";
        echo sprintf("class %sTest extends \PHPUnit\Framework\TestCase\n{\n", $info->getShortClassName());

        echo "    /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager */\n";
        echo "    private \$objectManager;\n\n";


        echo "    /** @var {$info->getClassName()} */\n";
        echo "    private \$object;\n\n";

        foreach ($info->getConstructorArguments() as $argument) {
            echo "    /** @var {$argument[0]}|\PHPUnit\Framework\MockObject\MockObject */\n";
            echo "    private \${$argument[1]};\n\n";
        }

        echo "    protected function setUp()\n";
        echo "    {\n";
        echo "         \$this->objectManager = new ObjectManager(\$this);\n\n";
        foreach ($info->getConstructorArguments() as $argument) {
            echo "         \$this->{$argument[1]} = \$this->createMock({$argument[0]}::class);\n";
        }
        echo "\n";
        echo "         \$this->object = \$this->objectManager->get({$info->getClassName()}::class, [\n";
        foreach ($info->getConstructorArguments() as $argument) {
            echo "             '{$argument[1]}' => \$this->{$argument[1]},\n";
        }
        echo "         ]);\n";
        echo "    }\n";
        echo "}\n";

        return ob_get_clean();
    }
}