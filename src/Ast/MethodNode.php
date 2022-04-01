<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class MethodNode implements INode
	{
		/** @var string */
		private $indentation;

		/** @var IMethodFlag[] */
		private $flags;

		/** @var string */
		private $keywordPrefix;

		/** @var string */
		private $keyword;

		/** @var Name */
		private $name;

		/** @var FunctionArguments */
		private $arguments;

		/** @var FunctionReturnType|NULL */
		private $returnType;

		/** @var IFunctionBody */
		private $body;


		/**
		 * @param string $indentation
		 * @param IMethodFlag[] $flags
		 * @param string $keywordPrefix
		 * @param string $keyword
		 */
		public function __construct(
			$indentation,
			array $flags,
			$keywordPrefix,
			$keyword,
			Name $name,
			FunctionArguments $arguments,
			FunctionReturnType $returnType = NULL,
			IFunctionBody $body
		)
		{
			Assert::string($indentation);
			Assert::string($keywordPrefix);
			Assert::string($keyword);

			$this->indentation = $indentation;
			$this->flags = $flags;
			$this->keywordPrefix = $keywordPrefix;
			$this->keyword = $keyword;
			$this->name = $name;
			$this->arguments = $arguments;
			$this->returnType = $returnType;
			$this->body = $body;
		}


		/**
		 * @return string
		 */
		public function getName()
		{
			return $this->name->getName();
		}


		/**
		 * @param  string $name
		 * @return void
		 */
		public function setName($name)
		{
			$this->name = Name::fromName($this->name, $name);
		}


		public function toString()
		{
			$s = $this->indentation;

			foreach ($this->flags as $flag) {
				$s .= $flag->toString();
			}

			$s .= $this->keywordPrefix;
			$s .= $this->keyword;
			$s .= $this->name->toString();
			$s .= $this->arguments->toString();

			if ($this->returnType !== NULL) {
				$s .= $this->returnType->toString();
			}

			$s .= $this->body->toString();
			return $s;
		}


		/**
		 * @return self
		 */
		public static function parse(Flags $flags, NodeParser $parser)
		{
			$keyword = $parser->consumeTokenAsText(T_FUNCTION);
			$parser->consumeWhitespace();
			$name = Name::parse($parser->createSubParser());
			$parser->tryConsumeWhitespace();
			$arguments = FunctionArguments::parse($parser->createSubParser());
			$parser->tryConsumeWhitespace();
			$returnType = NULL;
			$body = NULL;

			if ($parser->isCurrent(':')) {
				$returnType = FunctionReturnType::parse($parser->createSubParser());
				$parser->tryConsumeWhitespace();
			}

			if ($parser->isCurrent('{')) {
				$body = FunctionBody::parse($parser->createSubParser());

			} elseif ($parser->isCurrent(';')) {
				$body = NoFunctionBody::parse($parser->createSubParser());

			} else {
				$parser->errorUnknowToken();
			}

			$parser->close();

			return new self(
				//$parser->getNodeIndentation(),
				$flags->getIndentation(),
				$flags->toMethodFlags(),
				$parser->getNodeIndentation(),
				$keyword,
				$name,
				$arguments,
				$returnType,
				$body
			);
		}
	}
