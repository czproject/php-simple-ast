<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class Type
	{
		/** @var string */
		private $indentation;

		/** @var NamedType[] */
		private $types;


		/**
		 * @param NamedType[] $types
		 */
		public function __construct(string $indentation, array $types)
		{
			Assert::true(count($types) > 0, 'Types cannot be empty.');

			$this->indentation = $indentation;
			$this->types = $types;
		}


		/**
		 * @return NamedType[]
		 */
		public function getNamedTypes(): array
		{
			return $this->types;
		}


		public function isNullable(): bool
		{
			foreach ($this->types as $namedType) {
				if ($namedType->isNullable()) {
					return TRUE;
				}
			}

			return FALSE;
		}


		public function setNullable(bool $nullable): void
		{
			$namedTypes = $this->types;

			if (count($namedTypes) > 1) {
				throw new \CzProject\PhpSimpleAst\InvalidStateException('Union types are not supported.');
			}

			foreach ($namedTypes as $namedType) {
				$namedType->setNullable($nullable);
			}
		}


		public function isSingle(): bool
		{
			return count($this->types) === 1;
		}


		public function toString(): string
		{
			$s = $this->indentation;

			foreach ($this->types as $name) {
				$s .= $name->toString();
			}

			return $s;
		}


		public static function parse(NodeParser $parser): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$types = [];
			$types[] = NamedType::parse($parser->createSubParser());
			$parser->tryConsumeWhitespace();

			while ($parser->isCurrent('&', '|') && !$parser->isAhead(T_ELLIPSIS, T_VARIABLE)) {
				$parser->consumeAsIndentation('&', '|');
				$parser->tryConsumeWhitespace();
				$types[] = NamedType::parse($parser->createSubParser());
				$parser->tryConsumeWhitespace();
			}

			$parser->close();
			return new self($nodeIndentation, $types);
		}
	}
