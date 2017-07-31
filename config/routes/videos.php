<?php
/**
 * This file is part of me-cms-youtube.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-cms-youtube
 * @license     https://opensource.org/licenses/mit-license.php MIT License
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
