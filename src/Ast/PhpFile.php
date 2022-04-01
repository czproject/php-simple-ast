<?php

	namespace CzProject\PhpSimpleAst\Ast;


	class PhpFile implements INode
	{
		/** @var string */
		private $path;

		/** @var PhpString */
		private $code;


		/**
		 * @param string $path
		 */
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
		 * @param  string $path
		 * @return self
		 */
		public static function parse($path, NodeParser $parser)
		{
			return new self($path, PhpString::parse($parser));
		}
	}
