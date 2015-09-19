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

<?php $this->assign('title', __('List videos')); ?>

<div class="youtubeVideos index">
	<?= $this->Html->h2(__('List videos')) ?>
    <table class="table table-hover">
		<thead>
			<tr>
				<th><?= $this->Paginator->sort('id') ?></th>
				<th><?= $this->Paginator->sort('user_id') ?></th>
				<th><?= $this->Paginator->sort('youtube_id') ?></th>
				<th><?= $this->Paginator->sort('category_id') ?></th>
				<th><?= $this->Paginator->sort('title') ?></th>
				<th><?= $this->Paginator->sort('subtitle') ?></th>
				<th><?= $this->Paginator->sort('priority') ?></th>
				<th class="actions text-center"><?= __('Actions') ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($youtubeVideos as $youtubeVideo): ?>
				<tr>
					<td><?= $this->Number->format($youtubeVideo->id) ?></td>
							<td>
						<?= $youtubeVideo->has('user') ? $this->Html->link($youtubeVideo->user->id, ['controller' => 'Users', 'action' => 'view', $youtubeVideo->user->id]) : '' ?>
					</td>
					<td><?= h($youtubeVideo->youtube_id) ?></td>
					<td><?= $this->Number->format($youtubeVideo->category_id) ?></td>
					<td><?= h($youtubeVideo->title) ?></td>
					<td><?= h($youtubeVideo->subtitle) ?></td>
					<td><?= $this->Number->format($youtubeVideo->priority) ?></td>
					<td class="actions">
						<?= $this->Html->button(NULL, ['action' => 'view', $youtubeVideo->id], ['icon' => 'eye', 'title' => __('View')]) ?>
						<?= $this->Html->button(NULL, ['action' => 'edit', $youtubeVideo->id], ['icon' => 'pencil', 'title' => __('Edit')]) ?>
						<?= $this->Form->postButton(NULL, ['action' => 'delete', $youtubeVideo->id], ['title' => __('Delete'), 'confirm' => __('Are you sure you want to delete # {0}?', $youtubeVideo->id), 'icon' => 'trash']) ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
    </table>
	<?= $this->element('MeTools.paginator') ?>
</div>