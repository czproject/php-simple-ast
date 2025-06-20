<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

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
		 * @param IPropertyModifier[] $promotedPropertyModifiers
		 */
		public function __construct(
			string $indentation,
			array $promotedPropertyModifiers,
			?Type $type,
			VariableName $name,
			?DefaultValue $defaultValue
		)
		{
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


		public function getType(): ?Type
		{
			return $this->type;
		}


		public function getDefaultValue(): ?DefaultValue
		{
			return $this->defaultValue;
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


		public static function parse(NodeParser $parser): self
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
