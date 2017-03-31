<?php
/**
 * This file is part of me-cms-youtube.
 *
 * me-cms-youtube is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * me-cms-youtube is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with me-cms-youtube.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
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
            'label' => __d('me_cms', 'Author'),
        ]);
    }

    echo $this->Form->control('category_id', [
        'empty' => false,
        'label' => __d('me_cms', 'Category'),
    ]);
    echo $this->Form->datetimepicker('created', [
        'label' => __d('me_cms', 'Date'),
        'help' => [
            __d('me_cms', 'If blank, the current date and time will be used'),
            __d('me_cms', 'You can delay the publication by entering a future date'),
        ],
        'value' => $video->created->i18nFormat(FORMAT_FOR_MYSQL),
    ]);
    echo $this->Form->control('priority', [
        'label' => __d('me_cms', 'Priority'),
    ]);
    echo $this->Form->control('is_spot', [
        'label' => sprintf('%s?', __d('me_cms_youtube', 'Is a spot')),
        'help' => __d('me_cms_youtube', 'Enable this option if this video is a spot'),
    ]);
    echo $this->Form->control('active', [
        'label' => sprintf('%s?', __d('me_cms', 'Published')),
        'help' => __d('me_cms', 'Disable this option to save as a draft'),
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
            'label' => __d('me_cms', 'Title'),
        ]);
        echo $this->Form->control('subtitle', [
            'label' => __d('me_cms', 'Subtitle'),
        ]);
        echo $this->Form->control('text', [
            'label' => __d('me_cms', 'Text'),
            'rows' => 8,
        ]);
    ?>
</fieldset>
<?= $this->Form->submit($title) ?>
<?= $this->Form->end() ?>