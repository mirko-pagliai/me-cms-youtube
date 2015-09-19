<?php $this->assign('title', __('Edit youtube video')); ?>

<?php $this->start('sidebar'); ?>
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $youtubeVideo->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $youtubeVideo->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List youtube videos'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New user'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
<?php $this->end('sidebar'); ?>

<div class="youtubeVideos form">
	<?= $this->Html->h2(__('Edit Youtube Video')) ?>
    <?= $this->Form->create($youtubeVideo); ?>
    <fieldset>
        <?php
            echo $this->Form->input('user_id', ['options' => $users]);
            echo $this->Form->input('youtube_id');
            echo $this->Form->input('category_id');
            echo $this->Form->input('title');
            echo $this->Form->input('subtitle');
            echo $this->Form->input('description');
            echo $this->Form->input('priority');
            echo $this->Form->input('active');
            echo $this->Form->input('is_spot');
        ?>
    </fieldset>
    <?= $this->Form->submit(__('Edit Youtube Video')) ?>
    <?= $this->Form->end() ?>
</div>