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
if (empty($categories) || $categories->count() < 2) {
    return;
}

$this->extend('MeCms./Common/widget');
$this->assign('title', __d('me_cms_youtube', 'Videos categories'));

$categories = $categories->map(function ($category) {
    return $this->Html->link($category->title, ['_name' => 'videosCategory', $category->slug]);
})->toArray();

echo $this->Html->ul($categories, ['icon' => 'caret-right']);
