<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class UnknowNode implements INode
	{
		/** @var string */
		private $content;


		public function __construct(string $content)
		{
			Assert::true($content !== '', 'Missing content.');

			$this->content = $content;
		}


		public function toString(): string
		{
			return $this->content;
		}
	}
