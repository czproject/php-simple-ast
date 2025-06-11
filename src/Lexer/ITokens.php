<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Lexer;


	interface ITokens
	{
		function hasToken(): bool;


		/**
		 * @throws \CzProject\PhpSimpleAst\OutOfRangeException
		 */
		function getCurrent(): IToken;


		function getNext(int $position = 0): ?IToken;


		function next(): ?IToken;


		function getTypeName(int|string $type): string;
	}
