<?php

namespace Stuifzand\TestGenerator\Model;

class ParseInfo
{
    /** @var string */
    private $className;

    /** @var array */
    private $constructorArguments = [];

    /**
     * @var bool
     */
    private $constructor;

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     */
    public function setClassName(string $className): void
    {
        $this->className = $className;
    }

    /**
     * @return array
     */
    public function getConstructorArguments(): array
    {
        return $this->constructorArguments;
    }

    /**
     * @param array $constructorArguments
     */
    public function setConstructorArguments(array $constructorArguments): void
    {
        $this->constructorArguments = $constructorArguments;
    }

    public function hasConstructor()
    {
        return $this->constructor;
    }

    public function setConstructor(bool $constructorFound)
    {
        $this->constructor = $constructorFound;
    }
}