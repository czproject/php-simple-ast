<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;


	class PhpFile implements IPhpSource
	{
		/** @var string */
		private $path;

		/** @var PhpString */
		private $code;


		public function __construct(string $path, PhpString $code)
		{
			$this->path = $path;
			$this->code = $code;
		}


		public function getNodes()
		{
			return $this->code->getNodes();
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


		public static function parse(string $path, NodeParser $parser): self
		{
			return new self($path, PhpString::parse($parser));
		}
	}
