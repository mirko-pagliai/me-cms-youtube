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
$this->assign('title', $title = __d('me_cms_youtube', 'Videos'));

$this->append('actions', $this->Html->button(
    I18N_ADD,
    ['action' => 'add'],
    ['class' => 'btn-success', 'icon' => 'plus']
));
$this->append('actions', $this->Html->button(
    __d('me_cms', 'Add category'),
    ['controller' => 'VideosCategories', 'action' => 'add'],
    ['class' => 'btn-success', 'icon' => 'plus']
));

$this->Library->datepicker(
    '#created',
    ['format' => 'MM-YYYY', 'viewMode' => 'years']
);
?>

<?= $this->Form->createInline(false, ['class' => 'filter-form', 'type' => 'get']) ?>
    <fieldset>
        <?= $this->Html->legend(I18N_FILTER, ['icon' => 'eye']) ?>
        <?php
            echo $this->Form->control('id', [
                'default' => $this->request->getQuery('id'),
                'placeholder' => I18N_ID,
                'size' => 2,
            ]);
            echo $this->Form->control('title', [
                'default' => $this->request->getQuery('title'),
                'placeholder' => I18N_TITLE,
                'size' => 16,
            ]);
            echo $this->Form->control('active', [
                'default' => $this->request->getQuery('active'),
                'empty' => I18N_ALL_STATUS,
                'options' => [
                    'yes' => I18N_ONLY_PUBLISHED,
                    'no' => I18N_ONLY_NOT_PUBLISHED,
                ],
            ]);
            echo $this->Form->control('user', [
                'default' => $this->request->getQuery('user'),
                'empty' => sprintf('-- %s --', __d('me_cms', 'all users')),
            ]);
            echo $this->Form->control('category', [
                'default' => $this->request->getQuery('category'),
                'empty' => sprintf('-- %s --', __d('me_cms', 'all categories')),
            ]);
            echo $this->Form->control('priority', [
                'default' => $this->request->getQuery('priority'),
                'empty' => sprintf('-- %s --', __d('me_cms', 'all priorities')),
            ]);
            echo $this->Form->datepicker('created', [
                'data-date-format' => 'YYYY-MM',
                'default' => $this->request->getQuery('created'),
                'placeholder' => __d('me_cms', 'month'),
                'size' => 5,
            ]);
            echo $this->Form->control('spot', [
                'default' => $this->request->getQuery('spot'),
                'hiddenField' => false,
                'label' => sprintf('%s?', __d('me_cms_youtube', 'Spot')),
                'type' => 'checkbox',
            ]);
            echo $this->Form->submit(null, ['icon' => 'search']);
        ?>
    </fieldset>
<?= $this->Form->end() ?>

<table class="table table-hover">
    <thead>
        <tr>
            <th class="text-center"><?= $this->Paginator->sort('id', I18N_ID) ?></th>
            <th><?= $this->Paginator->sort('title', I18N_TITLE) ?></th>
            <th class="text-center"><?= $this->Paginator->sort('Categories.title', I18N_CATEGORY) ?></th>
            <th class="text-center"><?= $this->Paginator->sort('Users.first_name', I18N_AUTHOR) ?></th>
            <th class="text-center hidden-xs"><?= $this->Paginator->sort('seconds', __d('me_cms_youtube', 'Duration')) ?></th>
            <th class="text-center"><?= $this->Paginator->sort('priority', I18N_PRIORITY) ?></th>
            <th class="text-center"><?= $this->Paginator->sort('created', I18N_DATE) ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($videos as $video) : ?>
            <tr>
                <td class="min-width text-center">
                    <code><?= $video->id ?></code>
                </td>
                <td>
                    <strong>
                        <?= $this->Html->link($video->title, ['action' => 'edit', $video->id]) ?>
                    </strong>

                    <?php
                    //If the video is a spot
                    if ($video->is_spot) {
                        echo $this->Html->span(
                            __d('me_cms_youtube', 'Spot'),
                            ['class' => 'record-label record-label-primary']
                        );
                    }

                    //If the video is scheduled
                    if ($video->created->isFuture()) {
                        echo $this->Html->span(
                            I18N_SCHEDULED,
                            ['class' => 'record-label record-label-warning']
                        );
                    }

                    //If the video is not active (it's a draft)
                    if (!$video->active) {
                        echo $this->Html->span(
                            I18N_DRAFT,
                            ['class' => 'record-label record-label-warning']
                        );
                    }

                    $actions = [];

                    //Only admins and managers can edit all videos.
                    //Users can edit only their own videos
                    if ($this->Auth->isGroup(['admin', 'manager']) ||
                        $this->Auth->hasId($video->user->id)
                    ) {
                        $actions[] = $this->Html->link(
                            I18N_EDIT,
                            ['action' => 'edit', $video->id],
                            ['icon' => 'pencil']
                        );
                    }

                    //Only admins and managers can delete videos
                    if ($this->Auth->isGroup(['admin', 'manager'])) {
                        $actions[] = $this->Form->postLink(
                            I18N_DELETE,
                            ['action' => 'delete', $video->id],
                            [
                                'class' => 'text-danger',
                                'icon' => 'trash-o',
                                'confirm' => I18N_SURE_TO_DELETE,
                            ]
                        );
                    }

                    //If the video is not a spot, is active and is not scheduled
                    if (!$video->is_spot && $video->active && !$video->created->isFuture()) {
                        $actions[] = $this->Html->link(
                            I18N_OPEN,
                            ['_name' => 'video', $video->id],
                            ['icon' => 'external-link', 'target' => '_blank']
                        );
                    } else {
                        $actions[] = $this->Html->link(
                            I18N_PREVIEW,
                            ['_name' => 'videosPreview', $video->id],
                            ['icon' => 'external-link', 'target' => '_blank']
                        );
                    }

                    echo $this->Html->ul($actions, ['class' => 'actions']);
                    ?>
                </td>
                <td class="text-center">
                    <?= $this->Html->link(
                        $video->category->title,
                        ['?' => ['category' => $video->category->id]],
                        ['title' => I18N_BELONG_ELEMENT]
                    ) ?>
                </td>
                <td class="text-center">
                    <?= $this->Html->link(
                        $video->user->full_name,
                        ['?' => ['user' => $video->user->id]],
                        ['title' => I18N_BELONG_USER]
                    ) ?>
                </td>
                <td class="min-width text-center hidden-xs">
                    <?= empty($video->duration) ? '00:00' : $video->duration ?>
                </td>
                <td class="min-width text-center">
                    <?php
                    switch ($video->priority) {
                        case '1':
                            echo $this->Html->badge('1', [
                                'class' => 'priority-verylow',
                                'tooltip' => __d('me_cms', 'Very low'),
                            ]);
                            break;
                        case '2':
                            echo $this->Html->badge('2', [
                                'class' => 'priority-low',
                                'tooltip' => __d('me_cms', 'Low'),
                            ]);
                            break;
                        case '4':
                            echo $this->Html->badge('4', [
                                'class' => 'priority-high',
                                'tooltip' => __d('me_cms', 'High'),
                            ]);
                            break;
                        case '5':
                            echo $this->Html->badge('5', [
                                'class' => 'priority-veryhigh',
                                'tooltip' => __d('me_cms', 'Very high'),
                            ]);
                            break;
                        default:
                            echo $this->Html->badge('3', [
                                'class' => 'priority-normal',
                                'tooltip' => __d('me_cms', 'Normal'),
                            ]);
                            break;
                    }
                    ?>
                </td>
                <td class="min-width text-center">
                    <div class="hidden-xs">
                        <?= $video->created->i18nFormat(getConfigOrFail('main.datetime.long')) ?>
                    </div>
                    <div class="visible-xs">
                        <div><?= $video->created->i18nFormat(getConfigOrFail('main.date.short')) ?></div>
                        <div><?= $video->created->i18nFormat(getConfigOrFail('main.time.short')) ?></div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->element('MeTools.paginator') ?>
