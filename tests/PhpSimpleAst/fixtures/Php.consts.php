<?php
	class Greeting
	{
		const A = 10;
		const B = 'foo';
		const C = 102;
		const D = array('foo');
		const E = ['foo', 'bar'];
		const F = NULL;

		private const G = FALSE;
	}


	interface IGreeting
	{
		const A = 10;
		const B = 'foo';
		const C = 102;
		const D = array('foo');
		const E = ['foo', 'bar'];
		const F = NULL;

		public const G = FALSE;
	}


	class Person
	{
	}
