<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Lexer;


	interface ITokens
	{
		/**
		 * @return bool
		 */
		function hasToken();


		/**
		 * @return IToken
		 * @throws \CzProject\PhpSimpleAst\OutOfRangeException
		 */
		function getCurrent();


		/**
		 * @return IToken|NULL
		 */
		function getNext();


		/**
		 * @return IToken|NULL
		 */
		function next();


		/**
		 * @param  int|string $type
		 * @return string
		 */
		function getTypeName($type);
	}
