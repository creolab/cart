<?php namespace Creolab\Cart;

use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;

class Cart extends Collection {

	/**
	 * Initialize the cart collection
	 */
	public function __construct(array $items)
	{
		if ($items)
		{
			foreach ($items as $item)
			{
				$this->items[] = new Item($item);
			}
		}
	}

}
