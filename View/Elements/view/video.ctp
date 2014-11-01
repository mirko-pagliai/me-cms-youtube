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
 * @copyright	Copyright (c) 2014, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeYoutube\View\Elements\view
 */
?>

<div class="video-container clearfix">
	<div class="content-header">
		<?php
			if(!empty($video['YoutubeVideo']['title']) && !empty($video['YoutubeVideo']['id']))
				echo $this->Html->h3($this->Html->link($video['YoutubeVideo']['title'],
					array('controller' => 'youtube_videos', 'action' => 'view', 'plugin' => 'me_youtube', $video['YoutubeVideo']['id']),
					array('class' => 'content-title')
				));
			
			if(!empty($video['YoutubeVideo']['subtitle']) && !empty($video['YoutubeVideo']['id']))
				echo $this->Html->h4($this->Html->link($video['YoutubeVideo']['subtitle'],
					array('controller' => 'youtube_videos', 'action' => 'view', 'plugin' => 'me_youtube', $video['YoutubeVideo']['id']),
					array('class' => 'content-subtitle')
				));
		?>
		<div class="content-info">
			<?php
				if(!empty($video['User']['first_name']) && !empty($video['User']['last_name']))
					echo $this->Html->div('content-author',
						__d('me_cms', 'Posted by %s',
						sprintf('%s %s', $video['User']['first_name'], $video['User']['last_name'])),
						array('icon' => 'user')
					);
				
				if(!empty($video['YoutubeVideo']['created']))
					echo $this->Html->div('content-date',
						__d('me_cms', 'Posted on %s', $this->Time->format($video['YoutubeVideo']['created'], $config['datetime']['long'])), 
						array('icon' => 'clock-o')
					);
			?>
		</div>
	</div>
	<div class="content-text">
		<?php
			if(!empty($video['YoutubeVideo']['youtube_id']))
				echo $this->Html->div('embed-responsive embed-responsive-16by9 margin-20', $this->Html->iframe(array(
					'class'				=> 'embed-responsive-item',
					'allowfullscreen'	=> TRUE,
					'src'				=> sprintf('http://www.youtube-nocookie.com/embed/%s?rel=0&amp;showinfo=0', $video['YoutubeVideo']['youtube_id']),
				)));

			if(!empty($video['YoutubeVideo']['description']))
				echo $video['YoutubeVideo']['description'];
		?>
	</div>
</div>