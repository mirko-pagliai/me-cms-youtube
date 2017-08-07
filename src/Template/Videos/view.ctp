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
$this->extend('MeCms./Common/view');
$this->assign('title', $video->title);

/**
 * Userbar
 */
if ($video->is_spot) {
    $this->userbar($this->Html->span(__d('me_cms_youtube', 'Spot'), ['class' => 'label label-primary']));
}
if (!$video->active) {
    $this->userbar($this->Html->span(I18N_DRAFT, ['class' => 'label label-warning']));
}
if ($video->created->isFuture()) {
    $this->userbar($this->Html->span(I18N_SCHEDULED, ['class' => 'label label-warning']));
}
$this->userbar($this->Html->link(
    __d('me_cms_youtube', 'Edit video'),
    ['action' => 'edit', $video->id, 'prefix' => ADMIN_PREFIX],
    ['icon' => 'pencil', 'target' => '_blank']
));
$this->userbar($this->Form->postLink(
    __d('me_cms_youtube', 'Delete video'),
    ['action' => 'delete', $video->id, 'prefix' => ADMIN_PREFIX],
    [
        'icon' => 'trash-o',
        'confirm' => I18N_SURE_TO_DELETE,
        'target' => '_blank',
    ]
));

/**
 * Breadcrumb
 */
if (getConfig('video.category')) {
    $this->Breadcrumbs->add($video->category->title, [
        '_name' => 'videosCategory',
        $video->category->slug,
    ]);
}
$this->Breadcrumbs->add($video->title, ['_name' => 'video', $video->slug]);

/**
 * Meta tags
 */
if ($this->request->isAction('view', 'Videos')) {
    $this->Html->meta(['content' => 'article', 'property' => 'og:type']);
    $this->Html->meta(['content' => $video->modified->toUnixString(), 'property' => 'og:updated_time']);

    if ($video->preview) {
        $this->Html->meta(['href' => $video->preview['preview'], 'rel' => 'image_src']);
        $this->Html->meta(['content' => $video->preview['preview'], 'property' => 'og:image']);
        $this->Html->meta(['content' => $video->preview['width'], 'property' => 'og:image:width']);
        $this->Html->meta(['content' => $video->preview['height'], 'property' => 'og:image:height']);
    }

    if ($video->text) {
        $this->Html->meta([
            'content' => $this->Text->truncate($video->text, 100, ['html' => true]),
            'property' => 'og:description',
        ]);
    }
}

echo $this->element('views/video', compact('video'));
