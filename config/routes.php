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
use Cake\Routing\Router;

Router::defaultRouteClass('DashedRoute');
Router::extensions('rss');

Router::scope('/', ['plugin' => ME_CMS_YOUTUBE], function ($routes) {
    //Requires other routes
    require 'routes' . DS . 'videos.php';
});

Router::plugin(ME_CMS_YOUTUBE, ['path' => '/me-cms-youtube'], function ($routes) {
    //Admin routes
    $routes->prefix(ADMIN_PREFIX, function ($routes) {
        //All admin routes
        $routes->fallbacks('DashedRoute');
    });
});
