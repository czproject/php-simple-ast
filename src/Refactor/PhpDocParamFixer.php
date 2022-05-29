<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Refactor;

	use CzProject\PhpSimpleAst\Reflection;
	use Nette\Utils\Strings;


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
			foreach ($classReflection->getMethods() as $methodReflection) {
				$docComment = $methodReflection->getDocComment();

				if ($docComment === NULL) {
					continue;
				}

				$index = 0;
				$newDocComment = Strings::replace($docComment, "#(\\s*\\*\\s*@param\\s+)([^@\\n\\r]*)#", function (array $m) use (&$index, $methodReflection) {
					$tag = $m[1];
					$value = $m[2];

					if (strpos($value, '$') !== FALSE) {
						return $m[0];
					}

					if ($methodReflection->hasParameterByIndex($index)) {
						$parameterReflection = $methodReflection->getParameterByIndex($index);
						$value .= ' $' . $parameterReflection->getName();

					} else {
						$tag = '';
						$value = '';
					}

					$index++;
					return $tag . $value;
				});

				if ($newDocComment !== $docComment) {
					$methodReflection->setDocComment($newDocComment);
				}
			}
		}
	}
