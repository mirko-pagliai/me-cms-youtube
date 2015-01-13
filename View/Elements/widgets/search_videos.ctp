<?php
/**
 * Search video widget.
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
	//Return, if the current view is the video search
	if($this->request->params['controller'] === 'videos' && $this->request->params['action'] === 'search')
		return;
?>

<div class="widget sidebar-widget">
	<?php 
		echo $this->Html->h4(__d('me_youtube', 'Search video'));

		echo $this->Form->createInline(FALSE, array('type' => 'get', 'url' => array('controller' => 'videos', 'action' => 'search', 'plugin' => 'me_youtube')));
		echo $this->Form->input('p', array(
			'default'		=> empty($pattern) ? NULL : $pattern,
			'placeholder'	=> sprintf('%s...', __d('me_cms', 'Search'))
		));
		echo $this->Form->end(NULL, array('class' => 'visible-lg-inline btn-primary', 'icon' => 'search'));
	?>
</div>