<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;


	interface IParentNode extends INode
	{
		/**
		 * @return array<INode|self>
		 */
		function getNodes();
	}
