<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Lexer;


	interface IToken
	{
		function isOfType(int|string $type): bool;


		function getType(): int|string;


		function toString(): string;


		function getPosition(): int;


		function getLine(): int;
	}
