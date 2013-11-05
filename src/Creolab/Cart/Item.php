<?php namespace Creolab\Cart;

class Item {

	/**
	 * Item data
	 * @var array
	 */
	protected $data;

	/**
	 * Init item
	 * @param array $data [description]
	 */
	public function __construct(array $data)
	{
		$this->data = $data;
	}

}
