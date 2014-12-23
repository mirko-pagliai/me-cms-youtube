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

<div class="video-container content-container clearfix">
	<?php
		$content_header = NULL;
		
		if(!empty($video['Category']['title']) && !empty($video['Category']['slug']))
			$content_header .= $this->Html->h5($this->Html->link($video['Category']['title'],
				array('controller' => 'videos', 'action' => 'index', 'plugin' => 'me_youtube', $video['Category']['slug'])),
				array('class' => 'content-category')
			);

		if(!empty($video['Video']['title']) && !empty($video['Video']['id']))
			$content_header .= $this->Html->h3($this->Html->link($video['Video']['title'],
				array('controller' => 'videos', 'action' => 'view', 'plugin' => 'me_youtube', $video['Video']['id'])),
				array('class' => 'content-title')
			);

		if(!empty($video['Video']['subtitle']) && !empty($video['Video']['id']))
			$content_header .= $this->Html->h4($this->Html->link($video['Video']['subtitle'],
				array('controller' => 'videos', 'action' => 'view', 'plugin' => 'me_youtube', $video['Video']['id'])),
				array('class' => 'content-subtitle')
			);

		$content_info = NULL;

		if(!empty($video['User']['first_name']) && !empty($video['User']['last_name']))
			$content_info .= $this->Html->div('content-author',
				__d('me_cms', 'Posted by %s',
				sprintf('%s %s', $video['User']['first_name'], $video['User']['last_name'])),
				array('icon' => 'user')
			);

		if(!empty($video['Video']['created']))
			$content_info .= $this->Html->div('content-date',
				__d('me_cms', 'Posted on %s', $this->Time->format($video['Video']['created'], $config['datetime']['long'])), 
				array('icon' => 'clock-o')
			);

		if(!empty($content_info))
			$content_header .= $this->Html->div('content-info', $content_info);

		if(!empty($content_header))
			echo $this->Html->div('content-header', $content_header);
	?>
	<div class="content-text">
		<?php
			if(!empty($video['Video']['youtube_id'])) {
				//Sets the video source
				$src = sprintf('https://www.youtube.com/embed/%s?modestbranding=1&rel=0&color=white&theme=light', $video['Video']['youtube_id']);
				
				//Adds the autoplay
				if(!empty($autoplay))
					$src .= '&autoplay=1';
				
				echo $this->Html->div('embed-responsive embed-responsive-16by9 margin-20', 
					$this->Html->iframe(array(
						'class'				=> 'embed-responsive-item',
						'allowfullscreen'	=> TRUE,
						'src'				=> $src,
						'type'				=> 'text/html'
					))
				);
			}

			if(!empty($video['Video']['description']))
				echo $video['Video']['description'];
		?>
	</div>
</div>