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
 * @package		MeYoutube\View\Videos\rss
 */
?>
	
<?php
	$this->set(array(
		'documentData'	=> array('xmlns:dc' => 'http://purl.org/dc/elements/1.1/'),
		'channelData'	=> array(
			'title'			=> __d('me_youtube', 'Latest videos'),
			'link'			=> $this->Html->url('/', TRUE),
			'description'	=> __d('me_youtube', 'Latest videos'),
			'language'		=> 'en-us'
		)
	));

	foreach($videos as $video) {
		//Sets post link
		$link = array('controller' => 'videos', 'action' => 'view', $video['Video']['id'], 'plugin' => 'me_youtube');
		
		//Sets post text
		$text = $this->Text->truncate(strip_tags($video['Video']['description']), $config['truncate_to'], array(
			'ending' => '...', 'exact' => FALSE, 'html' => TRUE
		));
		
		//Adds the preview image to the text
		if(!empty($video['Video']['preview']))
			$text = $this->Html->thumb($video['Video']['preview'], array('width' => 200)).$text;

		echo $this->Rss->item(array(), array(
			'description'	=> $text,
			'guid'			=> array('url' => $link, 'isPermaLink' => 'true'),
			'link'			=> $link,
			'pubDate'		=> $video['Video']['created'],
			'title'			=> $video['Video']['title']
		));
	}
?>