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
 * @copyright	Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeYoutube\View\Helper;

use Cake\View\Helper;
use MeCms\View\Helper\AuthHelper;

/**
 * MenuDefault Helper.
 * 
 * This helper contains methods that will be called automatically to generate the menu of the backend.
 * You do not need to call these methods manually.
 */
class MeYoutubeMenuHelper extends Helper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = ['MeCms.Auth', 'Html' => ['className' => 'MeTools.Html']];
	
	/**
	 * Internal function to generate the menu for "videos" actions
	 * @return mixed Array with menu, title and link options
	 * @uses MeCms\View\Helper\AuthHelper::isGroup()
	 * @uses MeTools\View\Helper\HtmlHelper::link()
	 */
	public function _videos() {
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