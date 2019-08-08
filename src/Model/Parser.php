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

        $classIndex  = Parser::findToken($tokens, $nsIndex, count($tokens), T_CLASS);
        $stringIndex = Parser::findToken($tokens, $classIndex, count($tokens), T_STRING);
        $info->setClassName($ns . $tokens[$stringIndex][1]);

        $constructorFound = false;

        $start = $classIndex + 3;
        while ($start != count($tokens)) {
            $publicIndex = Parser::findToken($tokens, $start, count($tokens), T_PUBLIC);
            if ($publicIndex === count($tokens)) {
                break;
            }
            $functionIndex = Parser::findToken($tokens, $publicIndex, count($tokens), T_FUNCTION);
            if (Parser::distance($tokens, $publicIndex, $functionIndex) == 1) { // skip whitespace and comments?
                $stringIndex = Parser::findToken($tokens, $functionIndex, count($tokens), T_STRING);
                if ($stringIndex == count($tokens)) {
                    break;
                }
                if ($tokens[$stringIndex][1] === '__construct') {
                    $constructorFound = true;
                    break;
                }
                $start = $stringIndex + 1;
            } else {
                $start = $publicIndex + 1;
            }
        }

        $info->setConstructor($constructorFound);

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
            if (Parser::isTokenType($tokens, $first, T_WHITESPACE)) {
                $first++;
                continue;
            }
            if (Parser::isTokenType($tokens, $first, $type)) {
                break;
            }
            $first++;
        }

        return $first;
    }

    private static function distance(array $tokens, int $from, int $to)
    {
        $distance = 0;

        while ($from != $to) {
            if (Parser::isTokenType($tokens, $from, T_WHITESPACE)) {
                $from++;
                continue;
            }
            $from++;
            $distance++;
        }

        return $distance;
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