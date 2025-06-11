<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;


	class PropertyNode implements INode
	{
		/** @var string */
		private $indentation;

		/** @var IPropertyModifier[] */
		private $modifiers;

		/** @var Type|NULL */
		private $type;

		/** @var string */
		private $namePrefix;

		/** @var string */
		private $name;

		/** @var DefaultValue|NULL */
		private $defaultValue;

		/** @var string */
		private $closer;


		/**
		 * @param IPropertyModifier[] $modifiers
		 */
		public function __construct(
			string $indentation,
			array $modifiers,
			?Type $type,
			string $namePrefix,
			string $name,
			?DefaultValue $defaultValue,
			string $closer
		)
		{
			$this->indentation = $indentation;
			$this->modifiers = $modifiers;
			$this->type = $type;
			$this->namePrefix = $namePrefix;
			$this->name = $name;
			$this->defaultValue = $defaultValue;
			$this->closer = $closer;
		}


		public function toString(): string
		{
			$s = $this->indentation;

			foreach ($this->modifiers as $modifier) {
				$s .= $modifier->toString();
			}

			if ($this->type !== NULL) {
				$s .= $this->type->toString();
			}

			$s .= $this->namePrefix;
			$s .= $this->name;

			if ($this->defaultValue !== NULL) {
				$s .= $this->defaultValue->toString();
			}

			$s .= $this->closer;
			return $s;
		}


		public static function parse(Modifiers $modifiers, NodeParser $parser): self
		{
			$type = NULL;

			if (!$parser->isCurrent(T_VARIABLE)) {
				$type = Type::parse($parser->createSubParser());
				$parser->tryConsumeWhitespace();
			}

			$nodeIndentation = $parser->consumeNodeIndentation() . $parser->flushIndentation();
			$name = $parser->consumeTokenAsText(T_VARIABLE);
			$parser->tryConsumeWhitespace();

			$defaultValue = NULL;

			if ($parser->isCurrent('=')) {
				$defaultValue = DefaultValue::parseForProperty($parser->createSubParser());
			}

			$closer = $parser->flushIndentation() . $parser->consumeTokenAsText(';');
			$parser->close();

			return new self(
				$modifiers->getIndentation(),
				$modifiers->toPropertyModifiers(),
				$type,
				$nodeIndentation,
				$name,
				$defaultValue,
				$closer
			);
		}
	}
