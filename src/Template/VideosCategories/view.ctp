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
$this->extend('Videos/index');
$this->assign('title', $category->title);

/**
 * Userbar
 */
$this->userbar([
    $this->Html->link(
        __d('me_cms', 'Edit category'),
        ['action' => 'edit', 'prefix' => ADMIN_PREFIX, $category->id],
        ['icon' => 'pencil', 'target' => '_blank']
    ),
    $this->Form->postLink(
        __d('me_cms', 'Delete category'),
        ['action' => 'delete', 'prefix' => ADMIN_PREFIX, $category->id],
        [
            'class' => 'text-danger',
            'icon' => 'trash-o',
            'confirm' => __d('me_cms', 'Are you sure you want to delete this?'),
            'target' => '_blank',
        ]
    ),
]);

/**
 * Breadcrumb
 */
$this->Breadcrumbs->add($category->title, ['_name' => 'videosCategory', $category->title]);
