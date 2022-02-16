<?php

	namespace CzProject\PhpSimpleAst\Ast;


	class HtmlNode implements INode
	{
		/** @var string */
		private $text;


		/**
		 * @param string $text
		 */
		public function __construct($text)
		{
			$this->text = $text;
		}


		public function toString()
		{
			return $this->text;
		}
	}
