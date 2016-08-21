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
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */
use Cake\Routing\Router;

Router::defaultRouteClass('InflectedRoute');
Router::extensions('rss');

/**
 * MeYoutube routes
 */
Router::scope('/', ['plugin' => 'MeYoutube'], function ($routes) {
    //Categories
    if (!routeNameExists('videosCategories')) {
        $routes->connect(
            '/videos/categories',
            ['controller' => 'VideosCategories', 'action' => 'index'],
            ['_name' => 'videosCategories']
        );
    }
    
    //Category
    if (!routeNameExists('videosCategory')) {
        $routes->connect(
            '/videos/category/:slug',
            ['controller' => 'VideosCategories', 'action' => 'view'],
            [
                '_name' => 'videosCategory',
                'slug' => '[a-z0-9\-]+',
                'pass' => ['slug'],
            ]
        );
    }
    
    //Videos
    if (!routeNameExists('videos')) {
        $routes->connect(
            '/videos',
            ['controller' => 'Videos', 'action' => 'index'],
            ['_name' => 'videos']
        );
    }
    
    //Videos (RSS)
    if (!routeNameExists('videosRss')) {
        $routes->connect(
            '/videos/rss',
            ['controller' => 'Videos', 'action' => 'rss', '_ext' => 'rss'],
            ['_name' => 'videosRss']
        );
    }
    
    //Videos search
    if (!routeNameExists('videosSearch')) {
        $routes->connect(
            '/videos/search',
            ['controller' => 'Videos', 'action' => 'search'],
            ['_name' => 'videosSearch']
        );
    }
    
    //Videos by date
    if (!routeNameExists('videosByDate')) {
        $routes->connect(
            '/videos/:date',
            ['controller' => 'Videos', 'action' => 'indexByDate'],
            [
                '_name' => 'videosByDate',
                'date' => '(today|yesterday|\d{4}(\/\d{2}(\/\d{2})?)?)',
                'pass' => ['date'],
            ]
        );
    }
    
    //Video
    if (!routeNameExists('video')) {
        $routes->connect(
            '/video/:id',
            ['controller' => 'videos', 'action' => 'view'],
            ['_name' => 'video', 'id' => '\d+', 'pass' => ['id']]
        );
    }
    
    //Video preview
    if (!routeNameExists('videosPreview')) {
        $routes->connect(
            '/video/preview/:id',
            ['controller' => 'videos', 'action' => 'preview'],
            ['_name' => 'videosPreview', 'id' => '\d+', 'pass' => ['id']]
        );
    }

    /**
     * This allows backward compatibility for URLs like:
     * /videos/page:3
     * /videos/page:3/sort:Video.created/direction:desc
     * These URLs will become:
     * /videos?page=3
     */
    $routes->connect(
        '/videos/page::page/*',
        ['controller' => 'Videos', 'action' => 'indexCompatibility'],
        ['page' => '\d+', 'pass' => ['page']]
    );

    //Admin routes
    $routes->prefix('admin', function ($routes) {
        //Other admin routes
        $controllers = sprintf('(%s)', implode('|', [
            'videosCategories',
            'videos',
        ]));

        $routes->connect('/:controller', [], ['controller' => $controllers]);
        $routes->connect('/:controller/:action/*', [], ['controller' => $controllers]);
    });

    $routes->fallbacks();
});
