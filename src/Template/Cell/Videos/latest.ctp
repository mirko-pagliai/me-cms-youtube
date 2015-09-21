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
?>

<?php
	//Returns on index, except for category
	if($this->request->isAction('index', 'Videos') && !$this->request->param('slug'))
		return;
	
	//Returns on the last record view
	if(count($videos) < 2 && $this->request->isAction('view', 'Videos') && $this->request->param('id') && $videos[0]->id && $this->request->param('id') === $videos[0]->id)
		return;
?>

<?php if(count($videos)): ?>
	<div class="widget sidebar-widget">
		<?php
			echo $this->Html->h4(count($videos) > 1 ? __d('me_youtube', 'Latest {0} videos', count($videos)) : __d('me_youtube', 'Latest video'));
			
			foreach($videos as $video) {
				
				//Sets the thumbnail
				$thumb = $this->Thumb->img($video->preview, ['side' => 263]);

				//Sets the information about the video
				$info = $this->Html->strong($video->title);
				
				//Adds the description to the information about the video, if exists
				if($video->description)
					$info .= $this->Html->div(NULL, h($video->description));
				
				$info = $this->Html->div('info-wrapper', $this->Html->div('info-text', $info));
				
				echo $this->Html->div('video-preview', $this->Html->link($thumb.$info,
					['_name' => 'video', $video->id],
					['title' => $video->title]
				));
			}
		?>
	</div>
<?php endif; ?>