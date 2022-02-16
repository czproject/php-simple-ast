
# CzProject\Php-simple-ast

[![Tests Status](https://github.com/czproject/php-simple-ast/workflows/Tests/badge.svg)](https://github.com/czproject/php-simple-ast/actions)

Simple PHP-AST.

<a href="https://www.paypal.me/janpecha/5eur"><img src="https://buymecoffee.intm.org/img/button-paypal-white.png" alt="Buy me a coffee" height="35"></a>


## Installation

[Download a latest package](https://github.com/czproject/php-simple-ast/releases) or use [Composer](http://getcomposer.org/):

```
composer require czproject/php-simple-ast
```

CzProject\Php-simple-ast requires PHP 5.6.0 or later and ....


## Usage # OR Tips, Writing tests, ...

``` php
<?php
	$git = new Cz\Git\Git;
	$filename = __DIR__ . '/my-file.txt';
	file_put_contents($filename, "Lorem ipsum\ndolor\nsit amet");

	if($git->isChanges())
	{
		$git->add($filename)
			->commit('Added a file.');
	}
```

------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, https://www.janpecha.cz/
