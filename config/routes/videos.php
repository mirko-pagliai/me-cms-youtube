<?php
/**
 * This file is part of me-cms-youtube.
 *
 * me-cms-youtube is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * me-cms-youtube is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with me-cms-youtube.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */

//Categories
if (!$routes->nameExists('videosCategories')) {
    $routes->connect(
        '/videos/categories',
        ['controller' => 'VideosCategories', 'action' => 'index'],
        ['_name' => 'videosCategories']
    );
}

//Category
if (!$routes->nameExists('videosCategory')) {
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
if (!$routes->nameExists('videos')) {
    $routes->connect(
        '/videos',
        ['controller' => 'Videos', 'action' => 'index'],
        ['_name' => 'videos']
    );
}

//Videos by date
if (!$routes->nameExists('videosByDate')) {
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

//Videos (RSS)
if (!$routes->nameExists('videosRss')) {
    $routes->connect(
        '/videos/rss',
        ['controller' => 'Videos', 'action' => 'rss', '_ext' => 'rss'],
        ['_name' => 'videosRss']
    );
}

//Videos search
if (!$routes->nameExists('videosSearch')) {
    $routes->connect(
        '/videos/search',
        ['controller' => 'Videos', 'action' => 'search'],
        ['_name' => 'videosSearch']
    );
}

//Video
if (!$routes->nameExists('video')) {
    $routes->connect(
        '/video/:id',
        ['controller' => 'videos', 'action' => 'view'],
        ['_name' => 'video', 'id' => '\d+', 'pass' => ['id']]
    );
}

//Video preview
if (!$routes->nameExists('videosPreview')) {
    $routes->connect(
        '/video/preview/:id',
        ['controller' => 'videos', 'action' => 'preview'],
        ['_name' => 'videosPreview', 'id' => '\d+', 'pass' => ['id']]
    );
}
