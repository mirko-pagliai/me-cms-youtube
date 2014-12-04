<?php
/**
 * Routes.
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
 * @copyright	Copyright (c) 2014, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeYoutube\Config
 */

//Videos routes
Router::connect('/admin/videos/:action/*',	array('controller' => 'videos', 'plugin' => 'me_youtube', 'admin' => TRUE));
Router::connect('/videos/*',					array('controller' => 'videos', 'action' => 'index', 'plugin' => 'me_youtube'));
Router::connect('/video/*',					array('controller' => 'videos', 'action' => 'view', 'plugin' => 'me_youtube'));

Router::connect('/admin/videosCategories/:action/*',		array('controller' => 'videos_categories', 'plugin' => 'me_youtube', 'admin' => TRUE));
Router::connect('/videosCategories',						array('controller' => 'videos_categories', 'action' => 'index', 'plugin' => 'me_youtube'));