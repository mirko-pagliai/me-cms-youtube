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
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
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
	public $components = array(
		'MeTools.Xml',
		'Session'	=> array('className' => 'MeTools.MeSession')
	);
	
	/**
	 * Parses a video url and returns the video ID
	 * @param string $url Video url
	 * @return mixed Video ID or FALSE
	 */
	public function getId($url) {		
		//Parses the url
		$url = parse_url($url);
		
		if(empty($url['host']))
			return FALSE;
		
		//`youtube.com/watch?v=XXX` addresses
		if(preg_match('/youtube\.com$/', $url['host']) && !empty($url['query'])) {
			parse_str($url['query'], $query);
			
			return empty($query['v']) ? FALSE : $query['v'];
		}
		//`youtu.be/XXX` addresses
		elseif(preg_match('/youtu\.be$/', $url['host']) && !empty($url['path'])) {
			preg_match('/^\/([^\/]+)/', $url['path'], $matches);
			
			return empty($matches[1]) ? FALSE : $matches[1];
		}
		
		return FALSE;
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