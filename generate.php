<?php
require_once 'vendor/autoload.php';

use Stuifzand\TestGenerator\Model\Parser;

$parser = new Parser();
$info   = $parser->parse(file_get_contents(__DIR__ . '/../magento2/kata3/vendor/magento/module-catalog/Model/CategoryRepository.php'));

$generator = new \Stuifzand\TestGenerator\Model\Generator();
echo $generator->generate($info);
