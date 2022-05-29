<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class PropertyNode implements INode
	{
		/** @var string */
		private $indentation;

		/** @var IPropertyModifier[] */
		private $flags;

		/** @var NullableName|NULL */
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
		 * @param string $indentation
		 * @param IPropertyModifier[] $flags
		 * @param string $namePrefix
		 * @param string $name
		 * @param string $closer
		 */
		public function __construct(
			$indentation,
			array $flags,
			NullableName $type = NULL,
			$namePrefix,
			$name,
			DefaultValue $defaultValue = NULL,
			$closer
		)
		{
			Assert::string($indentation);
			Assert::string($namePrefix);
			Assert::string($name);
			Assert::string($closer);

			$this->indentation = $indentation;
			$this->flags = $flags;
			$this->type = $type;
			$this->namePrefix = $namePrefix;
			$this->name = $name;
			$this->defaultValue = $defaultValue;
			$this->closer = $closer;
		}


		public function toString()
		{
			$s = $this->indentation;

			foreach ($this->flags as $flag) {
				$s .= $flag->toString();
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


		/**
		 * @return self
		 */
		public static function parse(Flags $flags, NodeParser $parser)
		{
			$type = NULL;

			if (!$parser->isCurrent(T_VARIABLE)) {
				$type = NullableName::parse($parser->createSubParser());
				$parser->consumeWhitespace();
			}

			$name = $parser->consumeTokenAsText(T_VARIABLE);
			$parser->tryConsumeWhitespace();

			$defaultValue = NULL;

			if ($parser->isCurrent('=')) {
				$defaultValue = DefaultValue::parseForProperty($parser->createSubParser());
			}

			$closer = $parser->flushIndentation() . $parser->consumeTokenAsText(';');
			$parser->close();

			return new self(
				$flags->getIndentation(),
				$flags->toPropertyFlags(),
				$type,
				$parser->getNodeIndentation(),
				$name,
				$defaultValue,
				$closer
			);
		}
	}
