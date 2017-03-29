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
$this->extend('MeCms./Common/view');
$this->assign('title', $video->title);

/**
 * Userbar
 */
if ($video->is_spot) {
    $this->userbar($this->Html->span(__d('me_cms_youtube', 'Spot'), ['class' => 'label label-primary']));
}
if (!$video->active) {
    $this->userbar($this->Html->span(__d('me_cms', 'Draft'), ['class' => 'label label-warning']));
}
if ($video->created->isFuture()) {
    $this->userbar($this->Html->span(__d('me_cms', 'Scheduled'), ['class' => 'label label-warning']));
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
        'confirm' => __d('me_cms', 'Are you sure you want to delete this?'),
        'target' => '_blank',
    ]
));

/**
 * Breadcrumb
 */
if (config('video.category')) {
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
        $this->Html->meta(['href' => $video->preview, 'rel' => 'image_src']);
        $this->Html->meta(['content' => $video->preview, 'property' => 'og:image']);
    }

    if ($video->text) {
        $this->Html->meta([
            'content' => $this->Text->truncate($video->text, 100, ['html' => true]),
            'property' => 'og:description',
        ]);
    }
}

echo $this->element('views/video', compact('video'));
