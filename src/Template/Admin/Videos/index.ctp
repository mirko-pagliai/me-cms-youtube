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
	$this->assign('title', __d('me_cms', 'Videos'));
	$this->Library->datepicker('#created', ['format' => 'MM-YYYY', 'viewMode' => 'years']);
?>

<div class="videos index">
	<?= $this->Html->h2(__d('me_cms', 'Videos')) ?>
	<?= $this->Html->button(__d('me_cms', 'Add'), ['action' => 'add'], ['class' => 'btn-success', 'icon' => 'plus']) ?>
	
	<?= $this->Form->createInline(FALSE, ['class' => 'filter-form', 'type' => 'get']) ?>
		<fieldset>
			<?php
				echo $this->Form->legend(__d('me_cms', 'Filter'));
				echo $this->Form->input('title', [
					'default'		=> $this->request->query('title'),
					'placeholder'	=> __d('me_cms', 'title'),
					'size'			=> 16
				]);
				echo $this->Form->input('active', [
					'default'	=> $this->request->query('active'),
					'empty'		=> sprintf('-- %s --', __d('me_cms', 'all status')),
					'options'	=> ['yes' => __d('me_cms', 'Only published'), 'no' => __d('me_cms', 'Only drafts')]
				]);
				echo $this->Form->input('user', [
					'default'	=> $this->request->query('user'),
					'empty'		=> sprintf('-- %s --', __d('me_cms', 'all users'))
				]);
				echo $this->Form->input('category', [
					'default'	=> $this->request->query('category'),
					'empty'		=> sprintf('-- %s --', __d('me_cms', 'all categories'))
				]);
				echo $this->Form->input('priority', [
					'default'	=> $this->request->query('priority'),
					'empty'		=> sprintf('-- %s --', __d('me_cms', 'all priorities'))
				]);
				echo $this->Form->datepicker('created', [
					'data-date-format'	=> 'YYYY-MM',
					'default'			=> $this->request->query('created'),
					'placeholder'		=> __d('me_cms', 'month'),
					'size'				=> 5
				]);
				echo $this->Form->input('spot', [
					'default'	=> $this->request->query('spot'),
					'label'		=> __d('me_youtube', 'Spot?'),
					'type'		=> 'checkbox'
				]);
				echo $this->Form->submit(NULL, ['icon' => 'search']);
			?>
		</fieldset>
	<?= $this->Form->end() ?>
	
    <table class="table table-hover">
		<thead>
			<tr>
				<th><?= $this->Paginator->sort('title', __d('me_cms', 'Title')) ?></th>
				<th class="text-center"><?= $this->Paginator->sort('category_id', __d('me_cms', 'Category')) ?></th>
				<th class="text-center"><?= $this->Paginator->sort('User.full_name', __d('me_cms', 'Author')) ?></th>
				<th class="text-center"><?= $this->Paginator->sort('priority', __d('me_cms', 'Priority')) ?></th>
				<th class="text-center"><?= $this->Paginator->sort('created', __d('me_cms', 'Date')) ?></th>
				<th class="text-center"><?= $this->Paginator->sort('is_spot', __d('me_youtube', 'Spot')) ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($videos as $video): ?>
				<tr>
					<td>
						<?php
							$title = $this->Html->link($video->title, ['action' => 'edit', $video->id]);
						
							//If the video is not active (it's a draft)
							if(!$video->active)
								$title = sprintf('%s - %s', $title, $this->Html->span(__d('me_cms', 'Draft'), ['class' => 'text-warning']));
							
							echo $this->Html->strong($title);
														
							$actions = [];
							
							//Only admins and managers can edit all videos. Users can edit only their own videos
							if($this->Auth->isGroup(['admin', 'manager']) || $this->Auth->hasId($video->user->id))
								$actions[] = $this->Html->link(__d('me_cms', 'Edit'), ['action' => 'edit', $video->id], ['icon' => 'pencil']);					

							//Only admins and managers can delete videos
							if($this->Auth->isGroup(['admin', 'manager']))
								$actions[] = $this->Form->postLink(__d('me_cms', 'Delete'), ['action' => 'delete', $video->id], ['class' => 'text-danger', 'icon' => 'trash-o', 'confirm' => __d('me_cms', 'Are you sure you want to delete this?')]);

							//If the video is active (it's published)
							if($video->active)
								$actions[] = $this->Html->link(__d('me_cms', 'Open'), ['_name' => 'video', $video->id], ['icon' => 'external-link', 'target' => '_blank']);

							echo $this->Html->ul($actions, ['class' => 'actions']);
						?>
					</td>
					<td class="text-center"><?= $video->category->title ?></td>
					<td class="text-center"><?= $video->user->full_name ?></td>
					<td class="min-width text-center">
						<?php
							switch($video->priority) {
								case '1':
									echo $this->Html->badge('1', ['class' => 'priority-verylow', 'tooltip' => __d('me_cms', 'Very low')]);
									break;
								case '2':
									echo $this->Html->badge('2', ['class' => 'priority-low', 'tooltip' => __d('me_cms', 'Low')]);
									break;
								case '4':	
									echo $this->Html->badge('4', ['class' => 'priority-high', 'tooltip' => __d('me_cms', 'High')]);
									break;
								case '5':
									echo $this->Html->badge('5', ['class' => 'priority-veryhigh', 'tooltip' => __d('me_cms', 'Very high')]);
									break;
								default:
									echo $this->Html->badge('3', ['class' => 'priority-normal', 'tooltip' => __d('me_cms', 'Normal')]);
									break;
							}
						?>
					</td>
					<td class="min-width text-center">
						<?= $video->created->i18nFormat(config('main.datetime.long')) ?>
					</td>
					<td class="min-width text-center">
						<?php
							if($video->is_spot)
								echo $this->Html->badge(NULL, ['class' => 'priority-normal', 'icon' => 'check', 'tooltip' => __d('me_youtube', 'This video is a spot')]);
						?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
    </table>
	<?= $this->element('MeTools.paginator') ?>
</div>