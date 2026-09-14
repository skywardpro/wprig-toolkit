<?php

$config = new PhpCsFixer\Config();

$config->setRules([
	'yoda_style' => [
		'equal' => true,
		'identical' => true,
		'less_and_greater' => true,
	],
]);

$config->setFinder(
	PhpCsFixer\Finder::create()
		->in(__DIR__)
		->exclude(['vendor', 'node_modules']),
);

return $config;
