<?php
/**
 * Latest video widget.
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
 * @package		MeYoutube\View\Elements\widgets
 */
?>
	
<?php
	//Gets the latest video
	$video = $this->requestAction(array('controller' => 'videos', 'action' => 'request_latest', 'plugin' => 'me_youtube'));
	
	//Return, if the current view is that the last video
	if($this->request->params['controller'] === 'videos' && $this->request->params['action'] === 'view' 
		&& !empty($this->request->params['pass'][0]) && $this->request->params['pass'][0] === $video['Video']['id'])
		return;
?>

<?php if(!empty($video)): ?>
	<div class="widget sidebar-widget">
		<?php 
			echo $this->Html->h4(__d('me_youtube', 'Latest video'));
			
			//Sets the thumbnail
			$thumb = $this->Html->thumb($video['Video']['preview'], array('side' => 263));
			
			//Sets the information about the video
			$info = $this->Html->strong($video['Video']['title']);
			
			//Adds the description to the information about the video, if exists
			if(!empty($video['Video']['description']))
				$info .= $this->Html->div(NULL, h($video['Video']['description']));
			
			$info = $this->Html->div('info-wrapper', $this->Html->div('info-text', $info));
			
			echo $this->Html->div('video-preview', $this->Html->link($thumb.$info,
				array('controller' => 'videos', 'action' => 'view', 'plugin' => 'me_youtube', $video['Video']['id']),
				array('title' => $video['Video']['title'])
			));
		?>
	</div>
<?php endif; ?>