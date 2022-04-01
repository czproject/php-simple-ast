<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Lexer;


	interface IToken
	{
		/**
		 * @param  int|string $type
		 * @return bool
		 */
		function isOfType($type);


		/**
		 * @return int|string
		 */
		function getType();


		/**
		 * @return string
		 */
		function toString();


		/**
		 * @return int
		 */
		function getPosition();


		/**
		 * @return int
		 */
		function getLine();
	}
