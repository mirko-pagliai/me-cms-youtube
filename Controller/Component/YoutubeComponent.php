<?php
/**
 * YoutubeComponent.
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
 * @package		MeYoutube\Controller\Component
 */

App::uses('Component', 'Controller');
App::uses('CakeTime', 'Utility');

/**
 * It allows to get information about YouTube videos
 */
class YoutubeComponent extends Component {
	/**
	 * Components
	 * @var array
	 */
	public $components = array('MeTools.Xml');
	
	/**
	 * Parses a video url and returns the video ID
	 * @param string $url Video url
	 * @return mixed Video ID or FALSE
	 */
	public function getId($url) {
		//Parses the url
		$url = parse_url($url);
		
		//Checks if it's a YouTube address
		if(empty($url['host']) || !preg_match('/youtube\.com$/', $url['host'])) {
			$this->Session->flash(__d('me_youtube', 'This is not a %s video', 'YouTube'), 'error');
			return FALSE;
		}

		//Checks if it's a valid query address
		if(empty($url['query'])) {
			$this->Session->flash(__d('me_youtube', 'The video address is incorrect'), 'error');
			return FALSE;
		}

		$query = array();

		foreach(explode('&', $url['query']) as $string) {
			$exploded = explode('=', $string);
			$query[$exploded[0]] = $exploded[1];
		}

		//Checks if the video ID is present
		if(empty($query['v'])) {
			$this->Session->flash(__d('me_youtube', 'The video ID is not present'), 'error');
			return FALSE;
		}

		return $query['v'];
	}
	
	/**
	 * Gets information about a video
	 * @param string $id Video ID
	 * @return mixed information, otherwise FALSE
	 * @uses XmlComponent::getAsArray()
	 */
	public function getInfo($id) {
		$info = $this->Xml->getAsArray(sprintf('https://gdata.youtube.com/feeds/api/videos/%s?v=2', $id));
		
		if(empty($info))
			return FALSE;
		
		return array(
			'title' => empty($info['title']) ? NULL : $info['title'],
			'description' => empty($info['media:group']['media:description']['@']) ? NULL : $info['media:group']['media:description']['@'],
			'created' => empty($info['published']) ? NULL : CakeTime::format($info['published'], '%Y-%m-%d %H:%M')
		);
	}
}