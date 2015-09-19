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

<?php $this->assign('title', __('List videos categories')); ?>

<div class="youtubeVideosCategories index">
	<?= $this->Html->h2(__('List videos categories')) ?>
    <table class="table table-hover">
		<thead>
			<tr>
				<th><?= $this->Paginator->sort('id') ?></th>
				<th><?= $this->Paginator->sort('parent_id') ?></th>
				<th><?= $this->Paginator->sort('lft') ?></th>
				<th><?= $this->Paginator->sort('rght') ?></th>
				<th><?= $this->Paginator->sort('title') ?></th>
				<th><?= $this->Paginator->sort('slug') ?></th>
				<th><?= $this->Paginator->sort('description') ?></th>
				<th class="actions text-center"><?= __('Actions') ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($youtubeVideosCategories as $youtubeVideosCategory): ?>
				<tr>
					<td><?= $this->Number->format($youtubeVideosCategory->id) ?></td>
							<td>
						<?= $youtubeVideosCategory->has('parent_youtube_videos_category') ? $this->Html->link($youtubeVideosCategory->parent_youtube_videos_category->title, ['controller' => 'YoutubeVideosCategories', 'action' => 'view', $youtubeVideosCategory->parent_youtube_videos_category->id]) : '' ?>
					</td>
					<td><?= $this->Number->format($youtubeVideosCategory->lft) ?></td>
					<td><?= $this->Number->format($youtubeVideosCategory->rght) ?></td>
					<td><?= h($youtubeVideosCategory->title) ?></td>
					<td><?= h($youtubeVideosCategory->slug) ?></td>
					<td><?= h($youtubeVideosCategory->description) ?></td>
					<td class="actions">
						<?= $this->Html->button(NULL, ['action' => 'view', $youtubeVideosCategory->id], ['icon' => 'eye', 'title' => __('View')]) ?>
						<?= $this->Html->button(NULL, ['action' => 'edit', $youtubeVideosCategory->id], ['icon' => 'pencil', 'title' => __('Edit')]) ?>
						<?= $this->Form->postButton(NULL, ['action' => 'delete', $youtubeVideosCategory->id], ['title' => __('Delete'), 'confirm' => __('Are you sure you want to delete # {0}?', $youtubeVideosCategory->id), 'icon' => 'trash']) ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
    </table>
	<?= $this->element('MeTools.paginator') ?>
</div>