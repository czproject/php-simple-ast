<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;
	use CzProject\PhpSimpleAst\Helpers;


	class MethodNode implements INode
	{
		/** @var PhpDocNode|NULL */
		private $phpDocNode;

		/** @var string */
		private $indentation;

		/** @var IMethodModifier[] */
		private $modifiers;

		/** @var string */
		private $keywordPrefix;

		/** @var string */
		private $keyword;

		/** @var Name */
		private $name;

		/** @var Parameters */
		private $parameters;

		/** @var FunctionReturnType|NULL */
		private $returnType;

		/** @var IFunctionBody */
		private $body;


		/**
		 * @param string $indentation
		 * @param IMethodModifier[] $modifiers
		 * @param string $keywordPrefix
		 * @param string $keyword
		 */
		public function __construct(
			?PhpDocNode $phpDocNode,
			$indentation,
			array $modifiers,
			$keywordPrefix,
			$keyword,
			Name $name,
			Parameters $parameters,
			FunctionReturnType $returnType = NULL,
			IFunctionBody $body
		)
		{
			Assert::string($indentation);
			Assert::string($keywordPrefix);
			Assert::string($keyword);

			$this->phpDocNode = $phpDocNode;
			$this->indentation = $indentation;
			$this->modifiers = $modifiers;
			$this->keywordPrefix = $keywordPrefix;
			$this->keyword = $keyword;
			$this->name = $name;
			$this->parameters = $parameters;
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


		public function getDocComment(): ?string
		{
			return $this->phpDocNode !== NULL ? $this->phpDocNode->getContent() : NULL;
		}


		public function setDocComment(string $docComment): void
		{
			if ($this->phpDocNode !== NULL) {
				$this->phpDocNode->setContent($docComment);

			} else {
				$this->phpDocNode = new PhpDocNode($this->indentation, $docComment);
				$this->indentation = "\n" . Helpers::extractIndentation($this->indentation);
			}
		}


		/**
		 * @return Parameter[]
		 */
		public function getParameters(): array
		{
			return $this->parameters->getParameters();
		}


		public function isPublic(): bool
		{
			foreach ($this->modifiers as $modifier) {
				if (($modifier instanceof Visibility) && $modifier->isPublic()) {
					return TRUE;
				}
			}

			return FALSE;
		}


		public function isProtected(): bool
		{
			foreach ($this->modifiers as $modifier) {
				if (($modifier instanceof Visibility) && $modifier->isProtected()) {
					return TRUE;
				}
			}

			return FALSE;
		}


		public function isPrivate(): bool
		{
			foreach ($this->modifiers as $modifier) {
				if (($modifier instanceof Visibility) && $modifier->isPrivate()) {
					return TRUE;
				}
			}

			return FALSE;
		}


		public function setVisibilityToPublic(): void
		{
			foreach ($this->modifiers as $modifier) {
				if ($modifier instanceof Visibility) {
					$modifier->setAsPublic();
				}
			}
		}


		public function setVisibilityToProtected(): void
		{
			foreach ($this->modifiers as $modifier) {
				if ($modifier instanceof Visibility) {
					$modifier->setAsProtected();
				}
			}
		}


		public function setVisibilityToPrivate(): void
		{
			foreach ($this->modifiers as $modifier) {
				if ($modifier instanceof Visibility) {
					$modifier->setAsPrivate();
				}
			}
		}


		public function toString()
		{
			$s = $this->phpDocNode !== NULL ? $this->phpDocNode->toString() : '';
			$s .= $this->indentation;

			foreach ($this->modifiers as $modifier) {
				$s .= $modifier->toString();
			}

			$s .= $this->keywordPrefix;
			$s .= $this->keyword;
			$s .= $this->name->toString();
			$s .= $this->parameters->toString();

			if ($this->returnType !== NULL) {
				$s .= $this->returnType->toString();
			}

			$s .= $this->body->toString();
			return $s;
		}


		/**
		 * @return self
		 */
		public static function parse(
			?PhpDocNode $phpDocNode,
			Modifiers $modifiers,
			NodeParser $parser
		)
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$keyword = $parser->consumeTokenAsText(T_FUNCTION);
			$parser->consumeWhitespace();
			$name = Name::parse($parser->createSubParser());
			$parser->tryConsumeWhitespace();
			$parameters = Parameters::parse($parser->createSubParser());
			$parser->tryConsumeWhitespace();
			$returnType = NULL;
			$body = NULL;

			if ($parser->isCurrent(':')) {
				$returnType = FunctionReturnType::parse($parser->createSubParser());
				$parser->tryConsumeWhitespace();
			}

			if ($parser->isCurrent(T_COMMENT)) {
				$parser->consumeAsIndentation(T_COMMENT);
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
				$phpDocNode,
				$modifiers->getIndentation(),
				$modifiers->toMethodModifiers(),
				$nodeIndentation,
				$keyword,
				$name,
				$parameters,
				$returnType,
				$body
			);
		}
	}
