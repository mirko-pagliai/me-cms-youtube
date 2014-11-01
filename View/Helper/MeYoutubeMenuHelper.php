<?php
/**
 * MeYoutubeMenuHelper.
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
 * @package		MeYoutube\View\Helper
 */

App::uses('MenuHelper', 'MeCms.View/Helper');

/**
 * MeYoutubeMenu Helper.
 * 
 * It can be used to generate a menu for an action. It supports these types of menu: `ul`, `nav` and `dropdown`.
 */
class MeYoutubeMenuHelper extends MenuHelper {
	public function hello() {
		debug('hello!');
	}
	/**
	 * Internal function to generate the menu for "videos" actions.
	 * @param string $type Type of menu
	 * @return mixed Menu
	 * @uses link()
	 * @uses DropdownHelper::dropdown()
	 * @uses DropdownHelper::link()
	 */
	protected function _videos($type) {
		$menu = array(
			$this->link(__d('me_youtube', 'List videos'),	array('controller' => 'youtube_videos', 'action' => 'index', 'plugin' => 'me_youtube')),
			$this->link(__d('me_youtube', 'Add video'),		array('controller' => 'youtube_videos', 'action' => 'add', 'plugin' => 'me_youtube'))
		);
		
		if($type == 'dropdown')
			return $this->Dropdown->link(__d('me_youtube', 'Videos'), array('icon' => 'film')).PHP_EOL.$this->Dropdown->dropdown($menu);
		
		return $menu;
	}
}