<?php
require_once 'vendor/autoload.php';

use Stuifzand\TestGenerator\Model\Parser;

$filename = $argv[1];

$parser = new Parser();
$info   = $parser->parse(file_get_contents($filename));

$generator = new \Stuifzand\TestGenerator\Model\Generator();
echo $generator->generate($info);
