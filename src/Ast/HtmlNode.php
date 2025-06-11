<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;


	class HtmlNode implements INode
	{
		/** @var string */
		private $text;


		public function __construct(string $text)
		{
			$this->text = $text;
		}


		public function toString(): string
		{
			return $this->text;
		}
	}
