<?php

return function (JP\CodeChecker\CheckerConfig $config) {
	$config->setPhpVersion(new JP\CodeChecker\Version('8.0.0'));
	$config->addPath('./src');
	$config->addPath('./tests');
	$config->addIgnore('fixtures/*');
	JP\CodeChecker\Sets\CzProjectMinimum::configure($config);
};
