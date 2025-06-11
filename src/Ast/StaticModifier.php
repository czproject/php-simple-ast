<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class StaticModifier implements IMethodModifier, IPropertyModifier
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $modifier;


		public function __construct(string $indentation, string $modifier)
		{
			$lowerValue = strtolower($modifier);
			Assert::true($lowerValue === 'static');

			$this->indentation = $indentation;
			$this->modifier = $modifier;
		}


		public function toString(): string
		{
			return $this->indentation . $this->modifier;
		}


		public static function parse(NodeParser $parser): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$modifier = $parser->consumeTokenAsText(T_STATIC);
			$parser->close();
			return new self($nodeIndentation, $modifier);
		}
	}
