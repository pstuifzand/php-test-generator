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
        $this->beginFile();

        $this->emitNamespace($this->getTestClassNamespace($info));

        $this->emitUseStatement("Magento\Framework\TestFramework\Unit\Helper\ObjectManager");

        $this->emitNewline();

        $this->beginClass($info->getShortClassName() . "Test", "\PHPUnit\Framework\TestCase");

        $this->emitField("\Magento\Framework\TestFramework\Unit\Helper\ObjectManager", "objectManager");
        $this->emitField($info->getClassName(), "object");
        $this->emitConstructorArgumentsAsFields($info);

        $this->beginMethod("setUp");

        $this->emitFieldAssignment("objectManager", "new ObjectManager(\$this)");
        $this->emitNewline();

        $this->emitConstructorArgumentsAsFieldAssignments($info);
        $this->emitNewline();

        $this->emitFieldAssignment("object", function () use ($info) {
            $this->emitObjectManagerGet($info);
        });

        $this->endMethod();

        $this->endClass();

        return ob_get_clean();
    }

    private function beginFile(): void
    {
        echo "<?php\n\n";
    }

    /**
     * @param ParseInfo $info
     * @return string
     */
    private function getTestClassNamespace(ParseInfo $info): string
    {
        $testNamespace = explode('\\', $info->getNamespace());
        array_splice($testNamespace, 3, 0, ['Test', 'Unit']);
        array_shift($testNamespace);

        return implode('\\', $testNamespace);
    }

    /**
     * @param $namespace
     */
    private function emitNamespace($namespace): void
    {
        echo "namespace " . $namespace . ";\n\n";
    }

    private function emitNewline(): void
    {
        echo "\n";
    }

    /**
     * @param string $className
     */
    private function emitUseStatement(string $className): void
    {
        echo "use " . $className . ";\n";
    }

    /**
     * @param string $className
     * @param string $extends
     */
    private function beginClass(string $className, string $extends): void
    {
        echo "class " . $className . " extends " . $extends . "\n{\n";
    }

    /**
     * @param string|string[] $className
     * @param string $variable
     */
    private function emitField($className, string $variable): void
    {
        if (is_array($className)) {
            $className = implode('|', $className);
        }
        echo "    /** @var " . $className . " */\n";
        echo "    private \$" . $variable . ";\n\n";
    }

    /**
     * @param string $method
     */
    private function beginMethod(string $method): void
    {
        echo "    protected function " . $method . "()\n";
        echo "    {\n";
    }

    /**
     * @param string $fieldName
     * @param callable|string $code
     */
    private function emitFieldAssignment(string $fieldName, $code): void
    {
        echo "         \$this->" . $fieldName . " = ";
        if (is_callable($code)) {
            call_user_func($code);
        } else {
            echo $code;
        }
        echo ";\n";
    }

    /**
     * @param $argument
     * @return string
     */
    private function getClassMock($argument): string
    {
        $code = "\$this->createMock({$argument[0]}::class)";

        return $code;
    }

    private function endMethod(): void
    {
        echo "    }\n";
    }

    private function endClass(): void
    {
        echo "}\n";
    }

    /**
     * @param ParseInfo $info
     */
    private function emitObjectManagerGet(ParseInfo $info): void
    {
        echo "\$this->objectManager->get({$info->getClassName()}::class, [\n";
        foreach ($info->getConstructorArguments() as $argument) {
            echo "             '{$argument[1]}' => \$this->{$argument[1]},\n";
        }
        echo "         ])";
    }

    /**
     * @param ParseInfo $info
     */
    private function emitConstructorArgumentsAsFieldAssignments(ParseInfo $info): void
    {
        foreach ($info->getConstructorArguments() as $argument) {
            $this->emitFieldAssignment($argument[1], $this->getClassMock($argument));
        }
    }

    /**
     * @param ParseInfo $info
     */
    private function emitConstructorArgumentsAsFields(ParseInfo $info): void
    {
        foreach ($info->getConstructorArguments() as $argument) {
            $this->emitField([$argument[0], "\PHPUnit\Framework\MockObject\MockObject"], $argument[1]);
        }
    }
}