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
$this->extend('MeCms./Common/index');
$this->assign('title', $title = __d('me_cms_youtube', 'Search videos'));

/**
 * Breadcrumb
 */
$this->Breadcrumbs->add($title, ['_name' => 'videosSearch']);

echo $this->Form->create(null, [
    'type' => 'get',
    'url' => ['_name' => 'videosSearch']
]);
echo $this->Form->control('p', [
    'default' => $this->request->getQuery('p'),
    'label' => false,
    'placeholder' => sprintf('%s...', __d('me_cms', 'Search')),
]);
echo $this->Form->submit(__d('me_cms', 'Search'), [
    'class' => 'btn-primary visible-lg-inline',
    'icon' => 'search',
]);
echo $this->Form->end();
?>

<?php if (!empty($pattern)) : ?>
    <div class="bg-info mb4 padding-10">
        <?= __d('me_cms', 'You have searched for: {0}', $this->Html->em($pattern)) ?>
    </div>
<?php endif; ?>

<?php if (!empty($videos)) : ?>
    <div class="as-table">
        <?php foreach ($videos as $video) : ?>
            <div class="margin-10 padding-10">
                <?= $this->Html->link($video->title, ['_name' => 'video', $video->id]) ?>
                <span class="small text-muted">
                    (<?= $video->created->i18nFormat(getConfigOrFail('main.datetime.short')) ?>)
                </span>
                <div class="text-justify">
                    <?= $this->Text->truncate(strip_tags($video->text), 350, ['exact' => false, 'html' => true]) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?= $this->element('MeTools.paginator') ?>
<?php endif; ?>
