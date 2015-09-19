<?php
use Cake\Routing\Router;

Router::defaultRouteClass('InflectedRoute');

Router::scope('/', ['plugin' => 'MeYoutube'], function ($routes) {
	/**
	 * Admin routes
	 */
    $routes->prefix('admin', function ($routes) {
		/**
		 * Other admin routes
		 */
		$controllers = ['videos_categories', 'videos'];
		$controllers = sprintf('(%s)', implode('|', $controllers));
		
		$routes->connect('/:controller', [], ['controller' => $controllers]);
		$routes->connect('/:controller/:action/*', [], ['controller' => $controllers]);
    });

    $routes->fallbacks();
});