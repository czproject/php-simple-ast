<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class Flags
	{
		/** @var string */
		private $indentation;

		/** @var array<Visibility|OverridingFlag|StaticModifier> */
		private $flags;


		/**
		 * @param string $indentation
		 * @param array<Visibility|OverridingFlag|StaticModifier> $flags
		 */
		private function __construct($indentation, array $flags)
		{
			Assert::string($indentation);

			$this->indentation = $indentation;
			$this->flags = $flags;
		}


		/**
		 * @return string
		 */
		public function getIndentation()
		{
			return $this->indentation;
		}


		/**
		 * @return IMethodModifier[]
		 */
		public function toMethodFlags()
		{
			foreach ($this->flags as $flag) {
				Assert::type($flag, IMethodModifier::class, 'Flag ' . get_class($flag) . ' is not ' . IMethodModifier::class);
			}

			return $this->flags;
		}


		/**
		 * @return IPropertyModifier[]
		 */
		public function toPropertyFlags()
		{
			foreach ($this->flags as $flag) {
				Assert::type($flag, IPropertyModifier::class, 'Flag ' . get_class($flag) . ' is not ' . IPropertyModifier::class);
			}

			return $this->flags;
		}


		/**
		 * @param  string $indentation
		 * @return self
		 */
		public static function empty($indentation)
		{
			return new self($indentation, []);
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$flags = [];

			do {
				if ($parser->isCurrent(T_PUBLIC, T_PROTECTED, T_PRIVATE)) {
					$flags[] = Visibility::parse($parser->createSubParser());

				} elseif ($parser->isCurrent(T_STATIC)) {
					$flags[] = StaticModifier::parse($parser->createSubParser());

				} elseif ($parser->isCurrent(T_ABSTRACT, T_FINAL)) {
					$flags[] = OverridingFlag::parse($parser->createSubParser());

				} else {
					$parser->errorUnknowToken();
				}

				$parser->tryConsumeWhitespace();

			} while ($parser->isCurrent(T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC, T_ABSTRACT, T_FINAL));

			$parser->close();
			return new self($parser->getNodeIndentation(), $flags);
		}
	}
