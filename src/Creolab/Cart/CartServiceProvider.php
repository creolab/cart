<?php namespace Creolab\Cart;

use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the cart
	 * @return void
	 */
	public function boot()
	{
		$this->package('creolab/cart');

		// Register bindings
		$this->app->singleton('creolab.cart', function($app)
		{
			return new Cart($app['session'], $app['config']);
		});

		// Shortcut so developers don't need to add an Alias in app/config/app.php
		$alias = $this->app['config']->get('cart::alias');
		$this->app->booting(function() use ($alias)
		{
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();

			$loader->alias($alias, '\Creolab\Cart\CartFacade');
		});
	}

	/**
	 * Register the service provider.
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 * @return array
	 */
	public function provides()
	{
		return array('creolab.cart');
	}

}
