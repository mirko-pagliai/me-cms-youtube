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
 * @package		MeYoutube\View\VideosCategories
 */
?>

<?php $this->assign('sidebar', $this->MeYoutubeMenu->get('videos', 'nav')); ?>
	
<div class="videosCategories index">
	<?php
		echo $this->Html->h2(__d('me_youtube', 'Videos categories'));
		echo $this->Html->button(__d('me_cms', 'Add'), array('action' => 'add'), array('class' => 'btn-success', 'icon' => 'plus'));
	?>
	<table class="table table-striped">
		<tr>
			<th><?php echo __d('me_cms', 'Title'); ?></th>
			<th><?php echo __d('me_cms', 'Parent'); ?></th>
			<th class="min-width text-center"><?php echo __d('me_youtube', 'Videos'); ?></th>
		</tr>
		<?php foreach($categories as $category): ?>
			<tr>
				<td>
					<?php
						$title = $this->Html->link($category['VideosCategory']['title'], array('action' => 'edit', $id = $category['VideosCategory']['id']));
						echo $this->Html->strong($title);

						echo $this->Html->ul(array(
							$this->Html->link(__d('me_cms', 'Edit'), array('action' => 'edit', $id), array('icon' => 'pencil')),
							$this->Form->postLink(__d('me_cms', 'Delete'), array('action' => 'delete', $id), array('class' => 'text-danger', 'icon' => 'trash-o'), __d('me_cms', 'Are you sure you want to delete this?')),					
							$this->Html->link(__d('me_cms', 'Open'), array('controller' => 'videos', 'action' => 'index', $category['VideosCategory']['slug'], 'admin' => FALSE), array('icon' => 'external-link', 'target' => '_blank'))
						), array('class' => 'actions'));
					?>
				</td>
				<td><?php echo $category['Parent']['title']; ?></td>
				<td class="text-center"><?php echo $category['VideosCategory']['video_count']; ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php echo $this->element('MeTools.paginator'); ?>
</div>