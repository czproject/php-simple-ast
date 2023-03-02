<?php

	namespace App\Front\Presenters;

	use App\Base\Presenters;
	use App\Calc\CalculatorPresenter;
	use App\Calc\CalculatorPresenter as CalcPresenter;


	class MyPresenter extends Presenters\BasePresenter
	{
	}


	class FooPresenter extends CalculatorPresenter
	{
	}


	class BarPresenter extends CalcPresenter
	{
	}
