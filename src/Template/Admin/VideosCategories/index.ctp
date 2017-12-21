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
$this->extend('MeCms./Admin/Common/index');
$this->assign('title', $title = __d('me_cms_youtube', 'Videos categories'));

$this->append('actions', $this->Html->button(
    I18N_ADD,
    ['action' => 'add'],
    ['class' => 'btn-success', 'icon' => 'plus']
));
$this->append('actions', $this->Html->button(
    __d('me_cms', 'Add video'),
    ['controller' => 'Videos', 'action' => 'add'],
    ['class' => 'btn-success', 'icon' => 'plus']
));
?>

<table class="table table-hover">
    <thead>
        <tr>
            <th class="text-center"><?= I18N_ID ?></th>
            <th><?= I18N_TITLE ?></th>
            <th class="text-center"><?= __d('me_cms', 'Parent') ?></th>
            <th class="text-nowrap text-center"><?= __d('me_cms_youtube', 'Videos') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $category) : ?>
            <tr>
                <td class="text-nowrap text-center">
                    <code><?= $category->id ?></code>
                </td>
                <td>
                    <strong>
                        <?= $this->Html->link($category->title, ['action' => 'edit', $category->id]) ?>
                    </strong>

                    <?php
                        $actions = [
                            $this->Html->link(
                                I18N_EDIT,
                                ['action' => 'edit', $category->id],
                                ['icon' => 'pencil']
                            ),
                        ];

                        //Only admins can delete videos categories
                        if ($this->Auth->isGroup('admin')) {
                            $actions[] = $this->Form->postLink(
                                I18N_DELETE,
                                ['action' => 'delete', $category->id],
                                [
                                    'class' => 'text-danger',
                                    'icon' => 'trash-o',
                                    'confirm' => I18N_SURE_TO_DELETE,
                                ]
                            );
                        }

                        if ($category->video_count) {
                            $actions[] = $this->Html->link(
                                I18N_OPEN,
                                ['_name' => 'videosCategory', $category->slug],
                                ['icon' => 'external-link', 'target' => '_blank']
                            );
                        }

                        echo $this->Html->ul($actions, ['class' => 'actions']);
                    ?>
                </td>
                <td class="text-center">
                    <?php
                    if ($category->has('parent') && $category->parent->has('title')) {
                        echo $category->parent->title;
                    }
                    ?>
                </td>
                <td class="text-nowrap text-center">
                    <?php
                    if ($category->video_count) {
                        echo $this->Html->link(
                            $category->video_count,
                            ['controller' => 'Videos', 'action' => 'index', '?' => ['category' => $category->id]],
                            ['title' => I18N_BELONG_ELEMENT]
                        );
                    } else {
                        echo $category->video_count;
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->element('MeTools.paginator') ?>
