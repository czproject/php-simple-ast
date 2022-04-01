<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Lexer;


	class PhpToken implements IToken
	{
		/** @var int|string */
		private $type;

		/** @var string */
		private $text;

		/** @var int */
		private $position;

		/** @var int */
		private $line;


		/**
		 * @param int|string $type
		 * @param string $text
		 * @param int $position
		 * @param int $line
		 */
		public function __construct($type, $text, $position, $line)
		{
			$this->type = $type;
			$this->text = $text;
			$this->position = $position;
			$this->line = $line;
		}


		public function isOfType($type)
		{
			return $this->type === $type;
		}


		public function getType()
		{
			return $this->type;
		}


		public function toString()
		{
			return $this->text;
		}


		public function getPosition()
		{
			return $this->position;
		}


		public function getLine()
		{
			return $this->line;
		}
	}
