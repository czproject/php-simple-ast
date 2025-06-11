<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;


	class FunctionNode implements INode
	{
		/** @var PhpDocNode|NULL */
		private $phpDocNode;

		/** @var string */
		private $indentation;

		/** @var string */
		private $keyword;

		/** @var Name|NULL */
		private $name;

		/** @var Parameters */
		private $parameters;

		/** @var InheritedVariables|NULL */
		private $inheritedVariables;

		/** @var FunctionReturnType|NULL */
		private $returnType;

		/** @var FunctionBody */
		private $body;


		public function __construct(
			?PhpDocNode $phpDocNode,
			string $indentation,
			string $keyword,
			?Name $name,
			Parameters $parameters,
			?InheritedVariables $inheritedVariables,
			?FunctionReturnType $returnType,
			FunctionBody $body
		)
		{
			$this->phpDocNode = $phpDocNode;
			$this->indentation = $indentation;
			$this->keyword = $keyword;
			$this->name = $name;
			$this->parameters = $parameters;
			$this->returnType = $returnType;
			$this->inheritedVariables = $inheritedVariables;
			$this->body = $body;
		}


		public function getName(): ?string
		{
			return $this->name !== NULL
				? $this->name->getName()
				: NULL;
		}


		public function getDocComment(): ?string
		{
			return $this->phpDocNode !== NULL ? $this->phpDocNode->getContent() : NULL;
		}


		/**
		 * @return Parameter[]
		 */
		public function getParameters(): array
		{
			return $this->parameters->getParameters();
		}


		public function hasReturnType(): bool
		{
			return $this->returnType !== NULL;
		}


		public function toString(): string
		{
			$s = $this->phpDocNode !== NULL ? $this->phpDocNode->toString() : '';
			$s .= $this->indentation;
			$s .= $this->keyword;

			if ($this->name !== NULL) {
				$s .= $this->name->toString();
			}

			$s .= $this->parameters->toString();

			if ($this->inheritedVariables !== NULL) {
				$s .= $this->inheritedVariables->toString();
			}

			if ($this->returnType !== NULL) {
				$s .= $this->returnType->toString();
			}

			$s .= $this->body->toString();
			return $s;
		}


		public static function parse(
			?PhpDocNode $phpDocNode,
			NodeParser $parser
		): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$keyword = $parser->consumeTokenAsText(T_FUNCTION);
			$parser->consumeWhitespace();
			$name = Name::tryParseFunctionName($parser->createSubParser());
			$parser->tryConsumeWhitespace();
			$parameters = Parameters::parse($parser->createSubParser());
			$parser->tryConsumeWhitespace();
			$inheritedVariables = NULL;
			$returnType = NULL;
			$body = NULL;

			if ($name === NULL && $parser->isCurrent(T_USE)) {
				$inheritedVariables = InheritedVariables::parse($parser->createSubParser());
				$parser->tryConsumeWhitespace();
			}

			if ($parser->isCurrent(':')) {
				$returnType = FunctionReturnType::parse($parser->createSubParser());
				$parser->tryConsumeWhitespace();
			}

			if ($parser->isCurrent(T_COMMENT)) {
				$parser->consumeAsIndentation(T_COMMENT);
				$parser->tryConsumeWhitespace();
			}

			$body = FunctionBody::parse($parser->createSubParser());
			$parser->close();

			return new self(
				$phpDocNode,
				$nodeIndentation,
				$keyword,
				$name,
				$parameters,
				$inheritedVariables,
				$returnType,
				$body
			);
		}
	}
