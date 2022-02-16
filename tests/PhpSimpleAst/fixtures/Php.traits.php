<?php
	trait ezcReflectionReturnInfo {
	}

	class ezcReflectionMethod extends ReflectionMethod {
		use ezcReflectionReturnInfo;
		/* ... */
	}

	class ezcReflectionFunction extends ReflectionFunction {
		use ezcReflectionReturnInfo;
		/* ... */
	}


	// Multiple
	trait Hello {
	}

	trait World {
	}

	class MyHelloWorld {
		use Hello, World;
	}


	// Block { }
	trait HelloWorld {
	}

	class MyClass1 {
		use HelloWorld { sayHello as protected; }
	}

	class MyClass2 {
		use HelloWorld { sayHello as private myPrivateHello; }
	}
