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

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Network\Exception\InternalErrorException;
use Cake\Utility\Hash;

//Sets the default me-cms-youtube name
if (!defined('ME_CMS_YOUTUBE')) {
    define('ME_CMS_YOUTUBE', 'MeCmsYoutube');
}

/**
 * Loads the me-cms-youtube configuration
 */
Configure::load(sprintf('%s.me_cms_youtube', ME_CMS_YOUTUBE));

//Merges with the configuration from application, if exists
if (is_readable(CONFIG . 'me_cms_youtube.php')) {
    Configure::load('me_cms_youtube');
}

//Merges with the MeCms configuration
Configure::write(MECMS, Hash::merge(config(MECMS), Configure::consume(ME_CMS_YOUTUBE)));

if (!config('Youtube.key') || config('Youtube.key') === 'your-key-here') {
    throw new InternalErrorException('YouTube API key is missing');
}

/**
 * Loads the cache configuration
 */
Configure::load(sprintf('%s.cache', ME_CMS_YOUTUBE));

//Merges with the configuration from application, if exists
if (is_readable(CONFIG . 'cache.php')) {
    Configure::load('cache');
}

//Adds all cache configurations
foreach (Configure::consume('Cache') as $key => $config) {
    //Drops cache configurations that already exist
    if (Cache::config($key)) {
        Cache::drop($key);
    }

    Cache::config($key, $config);
}
