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
if (empty($videos) || $videos->isEmpty()) {
    return;
}

$this->extend('MeCms./Common/widget');
$this->assign('title', __dn('me_cms_youtube', 'Latest video', 'Latest {0} videos', $videos->count(), $videos->count()));

foreach ($videos as $video) {
    $link = ['_name' => 'video', $video->id];
    $path = $video->preview['preview'];
    $title = $video->title;
    $text = $video->text;

    echo $this->Html->div('mb-4', $this->element(ME_CMS . '.views/photo-preview', compact('link', 'path', 'title', 'text')));
}
