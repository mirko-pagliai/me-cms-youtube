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
?>
	
<?php
	$this->set([
		'documentData'	=> ['xmlns:dc' => 'http://purl.org/dc/elements/1.1/'],
		'channelData'	=> [
			'title'			=> __d('me_youtube', 'Latest videos'),
			'link'			=> \Cake\Routing\Router::url('/', TRUE),
			'description'	=> __d('me_youtube', 'Latest videos'),
			'language'		=> 'en-us'
		]
	]);
	
	foreach($videos as $video) {
		//Sets link
		$link = ['_name' => 'video', $video->id];
		
		//Sets text
		$text = $this->Text->truncate(
			strip_tags($video->description),
			config('frontend.truncate_to'),
			['ending' => '...', 'exact' => FALSE, 'html' => TRUE]
		);
		
		//Adds the preview image
		if(!empty($video->preview))
			$text = sprintf('%s%s', $this->Thumb->img($video->preview, ['width' => 400]), '<br />'.PHP_EOL.$text);

		echo $this->Rss->item([], [
			'description'	=> $text,
			'guid'			=> ['url' => $link, 'isPermaLink' => 'true'],
			'link'			=> $link,
			'pubDate'		=> $video->created,
			'title'			=> $video->title
		]);
	}
?>