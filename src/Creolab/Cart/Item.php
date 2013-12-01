<?php namespace Creolab\Cart;

class Item {

	/**
	 * Item ID
	 * @var string
	 */
	protected $identifier;

	/**
	 * Item data
	 * @var array
	 */
	protected $data;

	/**
	 * Init item
	 * @param array $data
	 */
	public function __construct($identifier, array $data)
	{
		$this->identifier = $identifier;
		$this->data       = $data;

		// Attributes
		if ( ! isset($this->data['attributes'])) $this->data['attributes'] = array();
	}

	/**
	 * Update item data
	 * @param  array  $data
	 * @return void
	 */
	public function update(array $data)
	{
		if (isset($data['quantity'])) $this->data['quantity'] += $data['quantity'];

		$this->data = array_merge($this->data, array_except($data, 'quantity'));
	}

	/**
	 * Return total amount for item
	 * @return float
	 */
	public function total()
	{
		return $this->price * $this->quantity;
	}

	/**
	 * Get item attribute
	 * @param  string $key
	 * @return mixed
	 */
	public function attr($key)
	{
		return array_get($this->data, 'attributes.' . $key);
	}

	/**
	 * Get some date from cart item
	 * @param  mixed $key
	 * @return mixed
	 */
	public function __get($key)
	{
		if (isset($this->data[$key])) return $this->data[$key];
		elseif (isset($this->{$key})) return $this->{$key};
	}

	/**
	 * Return data
	 * @return array
	 */
	public function toArray()
	{
		return $this->data;
	}

}
