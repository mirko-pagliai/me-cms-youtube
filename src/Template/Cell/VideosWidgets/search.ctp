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
//Returns on search
if ($this->request->isUrl(['_name' => 'videosSearch'])) {
    return;
}

$this->extend('MeCms./Common/widget');
$this->assign('title', __d('me_cms_youtube', 'Search videos'));

echo $this->Form->createInline(false, [
    'type' => 'get',
    'url' => ['_name' => 'videosSearch'],
]);
echo $this->Form->control('p', [
    'button' => $this->Form->button(null, ['class' => 'btn-primary', 'icon' => 'search']),
    'id' => false,
    'label' => false,
    'placeholder' => sprintf('%s...', __d('me_cms', 'Search')),
]);
echo $this->Form->end();
