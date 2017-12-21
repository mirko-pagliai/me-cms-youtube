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
$this->extend(ME_CMS . './Common/index');
$this->assign('title', $title = __d('me_cms_youtube', 'Videos categories'));

/**
 * Breadcrumb
 */
$this->Breadcrumbs->add($title, ['_name' => 'videosCategories']);

$categories = $categories->map(function ($category) {
    return $this->Html->link($category->title, ['_name' => 'videosCategory', $category->slug]);
})->toList();

echo $this->Html->ul($categories, ['icon' => 'caret-right']);
