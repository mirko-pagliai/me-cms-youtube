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
namespace MeYoutube\Utility;

use Cake\Core\Configure;
use MeTools\Utility\Xml;

/**
 * An utility to get information about YouTube videos
 * 
 * You can use this utility by adding:
 * <code>
 * use MeYoutube\Utility\Youtube;
 * </code>
 */
class Youtube {
	/**
	 * Parses a video url and returns the video ID
	 * @param string $url Video url
	 * @return mixed Video ID or FALSE
	 */
	public static function getId($url) {
		if(preg_match('/youtube\.com/', $url)) {
			$url = parse_url($url);
			
			if(empty($url['query']))
				return FALSE;
			
			parse_str($url['query'], $url);
				
			return empty($url['v']) ? FALSE : $url['v'];
		}
		elseif(preg_match('/youtu.be\/(.+)$/', $url, $matches))
			return empty($matches[1]) ? FALSE : $matches[1];
		else
			return FALSE;
	}
	
	/**
	 * Gets information about a video
	 * @param string $id Video ID
	 * @return mixed Array of information, otherwise FALSE
	 * @uses MeTools\Utility\Xml::fromFile()
	 */
	public static function getInfo($id) {
		//See https://developers.google.com/youtube/v3/getting-started#partial
		$url = 'https://www.googleapis.com/youtube/v3/videos?id=%s&key=%s&part=snippet,contentDetails&fields=items(snippet(title,description,thumbnails(high(url))),contentDetails(duration))';
		$info = Xml::fromFile(sprintf($url, $id, config('Youtube.key')));
				
		if(empty($info['items'][0]) || empty($info['items'][0]['snippet']) || empty($info['items'][0]['contentDetails']))
			return FALSE;
				
		$info = am([
			'preview' => $info['items'][0]['snippet']['thumbnails']['high']['url']
		], $info['items'][0]['snippet'], $info['items'][0]['contentDetails']);
		
		unset($info['thumbnails']);
				
		preg_match('/PT(([0-9]+)M)?(([0-9]+)S)?/', $info['duration'], $matches);
		
		$mins = empty($matches[2]) ? "00" : sprintf("%02d", $matches[2]);
		$secs = empty($matches[4]) ? "00" : sprintf("%02d", $matches[4]);
		
		$info['seconds'] = (int)$mins*60+(int)$secs;
		$info['duration'] = sprintf('%s:%s', $mins, $secs);

		return $info;
	}
}