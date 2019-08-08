<?php

namespace Stuifzand\TestGenerator\Model;

class ParseInfo
{
    /** @var string */
    private $className;

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
}