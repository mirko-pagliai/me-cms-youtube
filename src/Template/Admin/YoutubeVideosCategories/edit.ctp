<?php $this->assign('title', __('Edit youtube videos category')); ?>

<?php $this->start('sidebar'); ?>
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $youtubeVideosCategory->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $youtubeVideosCategory->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List youtube videos categories'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List parent youtube videos categories'), ['controller' => 'YoutubeVideosCategories', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New parent youtube videos category'), ['controller' => 'YoutubeVideosCategories', 'action' => 'add']) ?> </li>
    </ul>
<?php $this->end('sidebar'); ?>

<div class="youtubeVideosCategories form">
	<?= $this->Html->h2(__('Edit Youtube Videos Category')) ?>
    <?= $this->Form->create($youtubeVideosCategory); ?>
    <fieldset>
        <?php
            echo $this->Form->input('parent_id', ['options' => $parentYoutubeVideosCategories, 'empty' => true]);
            echo $this->Form->input('lft');
            echo $this->Form->input('rght');
            echo $this->Form->input('title');
            echo $this->Form->input('slug');
            echo $this->Form->input('description');
            echo $this->Form->input('video_count');
        ?>
    </fieldset>
    <?= $this->Form->submit(__('Edit Youtube Videos Category')) ?>
    <?= $this->Form->end() ?>
</div>