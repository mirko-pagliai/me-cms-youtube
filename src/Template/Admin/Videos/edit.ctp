<?php
/**
 * This file is part of me-cms-youtube.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-cms-youtube
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
$this->extend('MeCms./Admin/Common/form');
$this->assign('title', $title = __d('me_cms_youtube', 'Edit video'));
$this->Library->datetimepicker();
?>

<?= $this->Form->create($video); ?>
<div class='float-form'>
    <?php
    //Only admins and managers can add videos on behalf of other users
    if ($this->Auth->isGroup(['admin', 'manager'])) {
        echo $this->Form->control('user_id', [
            'empty' => false,
            'label' => I18N_AUTHOR,
        ]);
    }

    echo $this->Form->control('category_id', [
        'empty' => false,
        'label' => I18N_CATEGORY,
    ]);
    echo $this->Form->datetimepicker('created', [
        'label' => I18N_DATE,
        'help' => [
            I18N_USE_CURRENT_DATETIME,
            I18N_DELAY_PUBLICATION,
        ],
    ]);
    echo $this->Form->control('priority', [
        'label' => I18N_PRIORITY,
    ]);
    echo $this->Form->control('is_spot', [
        'label' => sprintf('%s?', __d('me_cms_youtube', 'Is a spot')),
        'help' => __d('me_cms_youtube', 'Enable this option if this video is a spot'),
    ]);
    echo $this->Form->control('active', [
        'label' => I18N_PUBLISHED,
        'help' => I18N_HELP_DRAFT,
    ]);
    ?>
</div>

<fieldset>
    <div class="row margin-20 text-center">
        <div class="col-sm-6">
            <h4><?= __d('me_cms_youtube', 'Video') ?></h4>
            <?= $this->Html->youtube(
                $video->youtube_id,
                ['class' => 'center-block', 'height' => 315, 'width' => 560]
            ) ?>
        </div>
        <div class="col-sm-6">
            <h4><?= __d('me_cms_youtube', 'Thumbnail preview') ?></h4>
            <?= $this->Thumb->resize(
                $video->preview['preview'],
                ['height' => 315],
                ['class' => 'center-block']
            ) ?>
        </div>
    </div>
    <p>
        <?= $this->Html->link(
            __d('me_cms_youtube', 'Open on {0}', 'YouTube'),
            $video->youtube_url,
            ['icon' => 'external-link', 'target' => '_blank']
        ) ?>
    </p>

    <?php
        echo $this->Form->control('youtube_id', [
            'label' => __d('me_cms_youtube', '{0} ID', 'YouTube'),
            'readonly' => true,
            'type' => 'text',
        ]);
        echo $this->Form->control('duration', [
            'label' => __d('me_cms_youtube', 'Duration'),
            'readonly' => true,
        ]);
        echo $this->Form->control('title', [
            'label' => I18N_TITLE,
        ]);
        echo $this->Form->control('subtitle', [
            'label' => I18N_SUBTITLE,
        ]);
        echo $this->Form->control('text', [
            'label' => I18N_TEXT,
            'rows' => 8,
        ]);
    ?>
</fieldset>
<?= $this->Form->submit($title) ?>
<?= $this->Form->end() ?>