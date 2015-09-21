<?php
/**
 * This file is part of MeYoutube.
 *
 * MeYoutube is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeYoutube is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeYoutube.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */
use Cake\Routing\Router;

Router::defaultRouteClass('InflectedRoute');

Router::extensions('rss');

Router::scope('/', ['plugin' => 'MeYoutube'], function ($routes) {
	/**
	 * VideosCategories controller
	 */
	 $routes->connect('/videos/categories', ['controller' => 'VideosCategories', 'action' => 'index'], ['_name' => 'videos_categories']);

	/**
	 * Videos controller
	 */
	$routes->connect('/video/:id',
		['controller' => 'videos', 'action' => 'view'],
		['_name' => 'video', 'id' => '\d+', 'pass' => ['id']]
	);
	$routes->connect('/videos', ['controller' => 'Videos', 'action' => 'index'], ['_name' => 'videos']);
	$routes->connect('/videos/rss', ['controller' => 'Videos', 'action' => 'rss', '_ext' => 'rss']);
	$routes->connect('/videos/search', ['controller' => 'Videos', 'action' => 'search'], ['_name' => 'search_videos']);
	$routes->connect('/videos/:slug',
		['controller' => 'Videos', 'action' => 'index'],
		['_name' => 'videos_category', 'slug' => '[a-z0-9\-]+', 'pass' => ['slug']]
	);
	
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