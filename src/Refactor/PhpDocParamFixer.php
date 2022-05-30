<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Refactor;

	use CzProject\PhpSimpleAst\Reflection;
	use Nette\Utils\Strings;
	use PHPStan\PhpDocParser\Ast\PhpDoc;


	class PhpDocParamFixer
	{
		/**
		 * @param  Reflection\ClassReflection[] $classes
		 */
		public static function processClasses(array $classes): void
		{
			foreach ($classes as $class) {
				self::processClass($class);
			}
		}


		public static function processClass(Reflection\ClassReflection $classReflection): void
		{
			$phpDocParser = \CzProject\PhpSimpleAst\Utils\PhpDocParser::getInstance();

			foreach ($classReflection->getMethods() as $methodReflection) {
				$docComment = $methodReflection->getDocComment();

				if ($docComment === NULL) {
					continue;
				}

				$phpDoc = $phpDocParser->parse($docComment);
				$index = 0;
				$paramsTag = $phpDoc->getTagsByName('@param');

				foreach ($phpDoc->children as $k => $phpDocTag) {
					if (!($phpDocTag instanceof PhpDoc\PhpDocTagNode)) {
						continue;
					}

					if ($phpDocTag->name !== '@param') {
						continue;
					}

					if (!($phpDocTag->value instanceof PhpDoc\InvalidTagValueNode)) {
						$index++;
						continue;
					}

					$value = $phpDocTag->value->value;
					$typeString = (string) $phpDocParser->parseType($value);
					$description = Strings::substring($value, Strings::length($typeString));

					if ($methodReflection->hasParameterByIndex($index)) {
						$parameterReflection = $methodReflection->getParameterByIndex($index);
						$typeString .= ' $' . $parameterReflection->getName();

					} else {
						$index++;
						unset($phpDoc->children[$k]);
						continue;
					}

					$index++;
					$phpDocTag->value->value = $typeString . $description;
				}

				$newDocComment = (string) $phpDoc;

				if ($newDocComment !== $docComment) {
					$methodReflection->setDocComment($newDocComment);
				}
			}
		}
	}
