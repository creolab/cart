<?php namespace Creolab\Cart;

use Illuminate\Config\Repository;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Collection;

class Cart {

	/**
	 * ID of the cart
	 * @var mixed
	 */
	protected $id;

	/**
	 * Items in cart
	 * @var Collection
	 */
	protected $items;

	/**
	 * IoC session dependency
	 * @var SessionManager
	 */
	protected $session;

	/**
	 * IoC config dependency
	 * @var Repository
	 */
	protected $config;

	/**
	 * The key under which the cart ID is stored in the session
	 */
	const SESSION_KEY = 'shcart_';

	/**
	 * The key under which the cart ID is stored in the session
	 */
	const COOKIE_KEY = 'shcrtid';

	/**
	 * Initialize dependencies
	 * @param SessionManager $session
	 * @param Repository     $config
	 */
	public function __construct(SessionManager $session, Repository $config)
	{
		// Dependencies
		$this->session = $session;
		$this->config  = $config;

		// Resolve the cart ID
		$this->id = $this->id();

		// Get cart from storage
		$this->resolveStorage();
	}

	/**
	 * Create new cart
	 * @return void
	 */
	public function create(array $items)
	{
		$this->items = new Collection;

		if ($items)
		{
			foreach ($items as $id => $item)
			{
				$this->items[$id] = new Item($id, $item);
			}
		}

		// Refresh the cart
		$this->refresh();

		return $this;
	}

	/**
	 * Get current cart
	 * @return array
	 */
	public function get()
	{
		return $this;
	}

	/**
	 * Add item to cart
	 * @param integer $productId
	 */
	public function add(array &$item)
	{
		// Validate data
		if ($this->validItem($item))
		{
			// Check for attributes
			if ( ! isset($item['attributes'])) $item['attributes'] = array();

			// Generate item ID
			$itemId = $this->itemId($item);

			// Check if item exists
			if ($this->has($itemId))
			{
				$itemObject = $this->items->get($itemId);
				$itemObject->update($item);
			}
			else
			{
				$itemObject = new Item($itemId, $item);
				$this->items->put($itemId, $itemObject);
			}

			// Write to storage
			$this->store();

			return $itemObject;
		}
	}

	/**
	 * Return all items
	 * @return array
	 */
	public function items()
	{
		return $this->items;
	}

	/**
	 * Remove single item by ID
	 * @param  string $id
	 * @return void
	 */
	public function remove($id)
	{
		$this->items->forget($id);

		$this->refresh();
	}

	/**
	 * Empty items from cart
	 * @return void
	 */
	public function removeAll()
	{
		$this->items = new Collection;

		$this->refresh();
	}

	/**
	 * Destroy cart and regenerate the ID
	 * @return void
	 */
	public function destroy()
	{
		setcookie(self::COOKIE_KEY, null, -1, '/');

		$this->removeAll();
	}

	/**
	 * Get total price for cart
	 * @return float
	 */
	public function total()
	{
		$total = 0;

		foreach ($this->items as $item) $total += $item->total();

		return $total;
	}

	/**
	 * Get total quantity
	 * @return float
	 */
	public function quantity()
	{
		$count = 0;

		foreach ($this->items as $item) $count += $item->quantity;

		return $count;
	}

	/**
	 * Check if item exists in cart
	 * @param  string  $itemId
	 * @return boolean
	 */
	public function has($itemId)
	{
		return $this->items->has($itemId);
	}

	/**
	 * Refresh the cart
	 * @return Cart
	 */
	public function refresh()
	{
		$this->store();

		return $this;
	}

	/**
	 * Resolve the cart id
	 * @return string
	 */
	public function id()
	{
		// Try to get from cookie
		if (isset($_COOKIE[self::COOKIE_KEY])) return $this->id = $_COOKIE[self::COOKIE_KEY];

		// If not found we generate a new one
		$this->id = $id = md5(uniqid(null, true));
		setcookie(self::COOKIE_KEY, $id, 0, "/");

		return $id;
	}

	/**
	 * Resolve item ID
	 * @param  array  $item
	 * @return string
	 */
	public function itemId(array $item)
	{
		if ( ! $attributes = array_get($item, 'attributes')) $attributes = array();

        return md5($item['id'] . json_encode(ksort($attributes)));
	}

	/**
	 * Validate item array
	 * @param  array  $item
	 * @return boolean
	 */
	public function validItem(array $item)
	{
		return true;
	}

	/**
	 * Stores the cart in a session storage
	 * @return void
	 */
	protected function store()
	{
		return $this->session->put(self::SESSION_KEY . $this->id(), $this->toArray());
	}

	/**
	 * Get cart from session storage
	 * @return Cart
	 */
	public function resolveStorage()
	{
		$this->items = new Collection;

		$data = $this->session->get(self::SESSION_KEY . $this->id());

		if ($data) $this->create(array_get($data, 'items'));
	}

	/**
	 * Return array representation
	 * @return array
	 */
	public function toArray()
	{
		$data = array('items' => array());

		foreach ($this->items as $key => $item) $data['items'][$key] = $item->toArray();

		return $data;
	}

}
