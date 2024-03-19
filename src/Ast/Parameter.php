<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;
	use CzProject\PhpSimpleAst\Lexer\PhpToken;


	class Parameter implements IFunctionBody
	{
		/** @var string */
		private $indentation;

		/** @var IPropertyModifier[] */
		private $promotedPropertyModifiers;

		/** @var Type|NULL */
		private $type;

		/** @var VariableName */
		private $name;

		/** @var DefaultValue|NULL */
		private $defaultValue;


		/**
		 * @param string $indentation
		 * @param IPropertyModifier[] $promotedPropertyModifiers
		 */
		public function __construct(
			$indentation,
			array $promotedPropertyModifiers,
			Type $type = NULL,
			VariableName $name,
			DefaultValue $defaultValue = NULL
		)
		{
			Assert::string($indentation);

			$this->indentation = $indentation;
			$this->promotedPropertyModifiers = $promotedPropertyModifiers;
			$this->type = $type;
			$this->name = $name;
			$this->defaultValue = $defaultValue;
		}


		public function getName(): string
		{
			return $this->name->getName();
		}


		public function isPassedByReference(): bool
		{
			return $this->name->hasReference();
		}


		public function hasPromotedProperty(): bool
		{
			return count($this->promotedPropertyModifiers) > 0;
		}


		public function toString()
		{
			$s = $this->indentation;

			foreach ($this->promotedPropertyModifiers as $promotedPropertyModifier) {
				$s .= $promotedPropertyModifier->toString();
			}

			if ($this->type !== NULL) {
				$s .= $this->type->toString();
			}

			$s .= $this->name->toString();

			if ($this->defaultValue !== NULL) {
				$s .= $this->defaultValue->toString();
			}

			return $s;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$promotedPropertyModifiers = NULL;
			$type = NULL;

			if ($parser->isCurrent(T_PUBLIC, T_PROTECTED, T_PRIVATE, PhpToken::T_READONLY())) {
				$promotedPropertyModifiers = Modifiers::parse($parser->createSubParser());
			}

			if (!$parser->isCurrent(T_VARIABLE, T_ELLIPSIS, '&', PhpToken::T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG())) {
				$type = Type::parse($parser->createSubParser());
				$parser->tryConsumeWhitespaceAndComments();
			}

			$name = VariableName::parse($parser->createSubParser());
			$parser->tryConsumeWhitespaceAndComments();
			$defaultValue = NULL;

			if ($parser->isCurrent('=')) { // default value
				$defaultValue = DefaultValue::parseForFunctionParameter($parser->createSubParser());
			}

			$parser->close();

			return new self(
				$nodeIndentation,
				$promotedPropertyModifiers !== NULL ? $promotedPropertyModifiers->toPropertyModifiers() : [],
				$type,
				$name,
				$defaultValue
			);
		}
	}
