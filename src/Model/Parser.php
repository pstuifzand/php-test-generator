<?php

namespace Stuifzand\TestGenerator\Model;

class Parser
{
    /**
     * @param string $classText
     * @return ParseInfo
     */
    public function parse(string $classText)
    {
        $info = new ParseInfo();

        $tokens  = token_get_all($classText);
        $nsIndex = Parser::findToken($tokens, 0, count($tokens), T_NAMESPACE);

        $ns = '';

        if ($nsIndex == count($tokens)) {
            $nsIndex = 0;
        } else {
            $ns = $this->getNamespace($tokens, $nsIndex + 2);
        }

        $classIndex = Parser::findToken($tokens, $nsIndex, count($tokens), T_CLASS);

        $info->setClassName($ns . $tokens[$classIndex + 2][1]);
        return $info;
    }

    /**
     * @param array $tokens
     * @param int $first token position of "namespace" in $tokens
     * @return string
     */
    private function getNamespace(array $tokens, int $first): string
    {
        $namespace = '\\';

        while ($first != count($tokens) && (is_array($tokens[$first]) || $tokens[$first] !== ';')) {
            if (is_array($tokens[$first])) {
                $namespace .= $tokens[$first][1];
            } else {
                $namespace .= $tokens[$first];
            }
            $first++;
        }

        return $namespace . '\\';
    }

    /**
     * @param array $tokens
     * @param int $first
     * @param int $last
     * @param int $type
     * @return int
     */
    private static function findToken(array $tokens, int $first, int $last, int $type)
    {
        while ($first != $last) {
            if (Parser::isTokenType($tokens, $first, $type)) {
                break;
            }
            $first++;
        }
        return $first;
    }

    /**
     * @param array $tokens
     * @param int $first
     * @param int $type
     * @return bool
     */
    private static function isTokenType(array $tokens, int $first, int $type): bool
    {
        $token = $tokens[$first];

        return is_array($token) && $token[0] === $type;
    }
}