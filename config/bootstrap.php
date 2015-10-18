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

use Cake\Cache\Cache;
use Cake\Core\Configure;

/**
 * MeYoutube configuration
 */
//Loads the configuration from the plugin
Configure::load('MeYoutube.me_youtube');

$config = Configure::read('MeYoutube');

//Loads the configuration from the application, if exists
if(is_readable(CONFIG.'me_youtube.php')) {
	Configure::load('me_youtube', 'default', FALSE);
	
	$config = \Cake\Utility\Hash::mergeDiff(Configure::consume('MeYoutube'), $config);
}

Configure::write('MeCms', \Cake\Utility\Hash::mergeDiff(Configure::read('MeCms'), $config));

/**
 * Youtube keys 
 */
//Loads the Youtube keys
Configure::load('youtube_keys');

/**
 * Cache configuration
 */
//Loads the cache configuration from the plugin
Configure::load('MeYoutube.cache');

//Loads the cache from the application, if exists
if(is_readable(CONFIG.'cache.php'))
	Configure::load('cache', 'default', FALSE);

//Adds all cache configurations
foreach(Configure::consume('Cache') as $key => $config) {
	//Drops the default cache
	if($key === 'default')
		Cache::drop('default');
	
	Cache::config($key, $config);
}