<?php $this->assign('title', __('List youtube videos categories')); ?>

<?php $this->start('sidebar'); ?>
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('New youtube videos category'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List parent youtube videos categories'), ['controller' => 'YoutubeVideosCategories', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New parent youtube videos category'), ['controller' => 'YoutubeVideosCategories', 'action' => 'add']) ?> </li>
    </ul>
<?php $this->end('sidebar'); ?>

<div class="youtubeVideosCategories index">
	<?= $this->Html->h2(__('List youtube videos categories')) ?>
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