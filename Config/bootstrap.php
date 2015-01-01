<?php
/**
 * Bootstrap.
 *
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
 * @package		MeYoutube\Config
 */

//Adds the admin routes prefix
Configure::write('Routing.prefixes', array('admin'));

/**
 * Default options (with File engine)
 */
$options = array(
	'duration'	=> '+999 days',
	'engine'	=> 'File',
	'mask'		=> 0666,
	'path'		=> CACHE.'me_youtube'.DS,
	'prefix'	=> NULL
);

/**
 * Default options (with Memcache engine)
 */
//$options = array(
//	'duration'	=> '+999 days',
//	'engine'	=> 'Memcache',
//	'prefix'	=> Inflector::slug(APP_DIR).'_'
//);

Cache::config('videos', am($options, array('groups' => array('videos'))));

//Default cache configuration
Cache::config('default', am($options, array('path' => CACHE)));