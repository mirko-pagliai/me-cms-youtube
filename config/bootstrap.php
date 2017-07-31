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
Configure::write(ME_CMS, Hash::merge(getConfig(ME_CMS), Configure::consume(ME_CMS_YOUTUBE)));

if (!getConfig('Youtube.key') || getConfig('Youtube.key') === 'your-key-here') {
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
    if (Cache::getConfig($key)) {
        Cache::drop($key);
    }

    Cache::setConfig($key, $config);
}
