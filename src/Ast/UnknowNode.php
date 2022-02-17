<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class UnknowNode implements INode
	{
		/** @var string */
		private $content;


		/**
		 * @param string $content
		 */
		public function __construct($content)
		{
			Assert::string($content);
			Assert::true($content !== '', 'Missing content.');
			$this->content = $content;
		}


		public function toString()
		{
			return $this->content;
		}
	}
