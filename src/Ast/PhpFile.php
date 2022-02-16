<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\PhpSimpleAst\Lexer;


	class PhpFile implements INode
	{
		/** @var string */
		private $path;

		/** @var PhpString */
		private $code;


		public function __construct($path, PhpString $code)
		{
			$this->path = $path;
			$this->code = $code;
		}


		/**
		 * @return string
		 */
		public function getPath()
		{
			return $this->path;
		}


		public function toString()
		{
			return $this->code->toString();
		}


		/**
		 * @return void
		 */
		public function save()
		{
			file_put_contents($this->path, $this->toString());
		}


		/**
		 * @return self
		 */
		public static function parse($path, Lexer\Stream $stream)
		{
			return new self($path, PhpString::parse($stream));
		}
	}
