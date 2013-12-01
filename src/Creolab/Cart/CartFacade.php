<?php namespace Creolab\Cart;

use Illuminate\Support\Facades\Facade;

class CartFacade extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'creolab.cart';
	}

}
