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
 * @package		MeYoutube\View\Videos
 */
?>

<div class="videos index">
	<?php
		echo $this->Html->h2(__d('me_youtube', 'Search videos'));
		
		echo $this->Form->create(FALSE, array('class' => 'margin-20', 'type' => 'get', 'url' => array('controller' => 'videos', 'action' => 'search', 'plugin' => 'me_youtube')));
		echo $this->Form->input('p', array(
			'default'		=> empty($pattern) ? NULL : $pattern,
			'label'			=> FALSE,
			'placeholder'	=> sprintf('%s...', __d('me_cms', 'Search'))
		));
		echo $this->Form->end(__d('me_cms', 'Search'), array(
			'class' => 'btn-primary visible-lg-inline',
			'icon'	=> 'search'
		));
	?>
	
	<?php if(!empty($pattern)): ?>
		<div class='bg-info margin-20 padding-10'>
			<?php
				echo $this->Html->div(NULL, __d('me_cms', 'You have searched for: %s', $this->Html->em($pattern)));
				
				if(!empty($count))
					echo $this->Html->div(NULL, __d('me_cms', 'Number of results found: %d', $count));
				else
					echo $this->Html->div(NULL,__d('me_cms', 'No results found'));
			?>
		</div>
	<?php endif; ?>
		
	<?php
		if(!empty($videos)) {
			$list = array();
			foreach($videos as $video) {
				$title = $this->Html->link($video['Video']['title'], array('action' => 'view', $video['Video']['id']));
				$list[] = $this->Html->div(NULL, 
					sprintf('%s - %s', $title, $this->Time->format($video['Video']['created'], $config['main']['datetime']['short'])).
					$this->Html->para('text-justify', $this->Text->truncate(strip_tags($video['Video']['description']), 350, array('exact' => FALSE, 'html' => TRUE)))
				);
			}

			echo $this->Html->ul($list, array(), array('icon' => 'caret-right'));
			
			echo $this->element('MeTools.paginator');
		}
	?>
</div>