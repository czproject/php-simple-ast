<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class Visibility implements IConstantModifier, IMethodModifier, IPropertyModifier
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $visibility;


		public function __construct(string $indentation, string $visibility)
		{
			$lowerValue = strtolower($visibility);
			Assert::true($lowerValue === 'public' || $lowerValue === 'protected' || $lowerValue === 'private', 'Invalid visibility');

			$this->indentation = $indentation;
			$this->visibility = $visibility;
		}


		public function isPublic(): bool
		{
			return strtolower($this->visibility) === 'public';
		}


		public function isProtected(): bool
		{
			return strtolower($this->visibility) === 'protected';
		}


		public function isPrivate(): bool
		{
			return strtolower($this->visibility) === 'private';
		}


		public function setAsPublic(): void
		{
			$this->visibility = 'public';
		}


		public function setAsProtected(): void
		{
			$this->visibility = 'protected';
		}


		public function setAsPrivate(): void
		{
			$this->visibility = 'private';
		}


		public function toString(): string
		{
			return $this->indentation . $this->visibility;
		}


		public static function parse(NodeParser $parser): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$visibility = $parser->consumeTokenAsText(T_PUBLIC, T_PROTECTED, T_PRIVATE);
			$parser->close();
			return new self($nodeIndentation, $visibility);
		}
	}
