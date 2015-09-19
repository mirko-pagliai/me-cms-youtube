<?php $this->assign('title', __('List youtube videos')); ?>

<?php $this->start('sidebar'); ?>
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('New youtube video'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New user'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
<?php $this->end('sidebar'); ?>

<div class="youtubeVideos index">
	<?= $this->Html->h2(__('List youtube videos')) ?>
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