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
namespace MeYoutube\View\Helper;

use MeCms\View\Helper\AuthHelper;
use MeCms\View\Helper\BaseMenuHelper;

/**
 * Menu Helper
 * 
 * It contains methods to generate menus for this plugin.
 * It supports these types of menu: `ul`, `collapse` and `dropdown`.
 * 
 * To generate a menu, you have to use the `get()` method. For example:
 * <code>
 * $this->Menu->get('photos', 'dropdown')
 * </code>
 */
class MenuHelper extends BaseMenuHelper {
	/**
	 * Internal function to generate the menu for "videos" actions.
	 * @param string $type Type of menu (optional, `ul`, `collapse` or `dropdown`)
	 * @return mixed Array with menu, title and link options
	 * @uses MeCms\View\Helper\AuthHelper::isGroup()
	 * @uses MeTools\View\Helper\HtmlHelper::link()
	 */
	public function _videos($type) {
		$menu = [
			$this->Html->link(__d('me_youtube', 'List videos'), ['controller' => 'videos', 'action' => 'index', 'plugin' => 'MeYoutube']),
			$this->Html->link(__d('me_youtube', 'Add video'), ['controller' => 'videos', 'action' => 'add', 'plugin' => 'MeYoutube'])
		];
		
		//Only admins and managers can access this controller
		if($this->Auth->isGroup(['admin', 'manager']))
			array_push($menu,
				$this->Html->link(__d('me_cms', 'List categories'), ['controller' => 'videos_categories', 'action' => 'index', 'plugin' => 'MeYoutube']),
				$this->Html->link(__d('me_cms', 'Add category'), ['controller' => 'videos_categories', 'action' => 'add', 'plugin' => 'MeYoutube'])
			);
		
		return [$menu, __d('me_youtube', 'Videos'), ['icon' => 'film']];
	}
}