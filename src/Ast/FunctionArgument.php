<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class FunctionArgument implements IFunctionBody
	{
		/** @var string */
		private $indentation;

		/** @var NullableName|NULL */
		private $type;

		/** @var VariableName */
		private $name;

		/** @var FunctionArgumentValue|NULL */
		private $defaultValue;


		/**
		 * @param string $indentation
		 */
		public function __construct(
			$indentation,
			NullableName $type = NULL,
			VariableName $name,
			FunctionArgumentValue $defaultValue = NULL
		)
		{
			Assert::string($indentation);

			$this->indentation = $indentation;
			$this->type = $type;
			$this->name = $name;
			$this->defaultValue = $defaultValue;
		}


		public function toString()
		{
			$s = $this->indentation;

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
			$type = NULL;

			if (!$parser->isCurrent(T_VARIABLE, '&')) {
				$type = NullableName::parse($parser->createSubParser());
			}

			$name = VariableName::parse($parser->createSubParser());
			$parser->tryConsumeWhitespace();
			$defaultValue = NULL;

			if ($parser->isCurrent('=')) { // default value
				$defaultValue = DefaultValue::parseForFunctionArgument($parser->createSubParser());
			}

			$parser->close();

			return new self(
				$parser->getNodeIndentation(),
				$type,
				$name,
				$defaultValue
			);
		}
	}
