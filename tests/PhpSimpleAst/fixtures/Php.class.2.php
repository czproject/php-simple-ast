<?php
	class Greeting implements IGreeting
	{
		public function say($name)
		{
			if (!$name) {
				throw new InvalidArgumentException('Invalid name');
			}
			return "Hello $name";
		}
	}

	$greeting = new Greeting;
	$greeting->say('John');
