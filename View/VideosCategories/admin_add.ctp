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
 * @package		MeYoutube\View\VideosCategories
 */
?>

<?php
	$this->assign('sidebar', $this->MeYoutubeMenu->get('videos', 'nav'));
	$this->Library->slugify();
?>

<div class="videosCategories form">
	<?php echo $this->Html->h2(__d('me_youtube', 'Add videos category')); ?>
	<?php echo $this->Form->create('VideosCategory'); ?>
		<div class='float-form'>
			<?php
				echo $this->Form->input('parent_id', array(
					'label' => __d('me_cms', 'Parent category'),
					'tip'	=> __d('me_cms', 'Leave blank to create a parent category')
				));
			?>
		</div>
		<fieldset>
			<?php
				echo $this->Form->input('title', array(
					'id' => 'title'
				));
				echo $this->Form->input('slug', array(
					'id'	=> 'slug',
					'tip'	=> __d('me_cms', 'The slug is a string identifying a resource. If you do not have special needs, let it be generated automatically')
				));
				echo $this->Form->input('description', array(
					'rows' => 3,
					'type' => 'textarea'
				));
			?>
		</fieldset>
	<?php echo $this->Form->end(__d('me_youtube', 'Add videos category')); ?>
</div>