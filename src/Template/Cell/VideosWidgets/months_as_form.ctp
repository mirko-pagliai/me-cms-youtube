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
if (empty($months) || $months->count() < 2) {
    return;
}

$this->extend('MeCms./Common/widget');
$this->assign('title', __d('me_cms_youtube', 'Videos by month'));

echo $this->Form->create(false, [
    'type' => 'get',
    'url' => ['_name' => 'videosByDate', sprintf('%s/%s', date('Y'), date('m'))],
]);
echo $this->Form->control('q', [
    'id' => false,
    'label' => false,
    'onchange' => 'send_form(this)',
    'options' => $months->map(function ($month) {
        return sprintf('%s (%s)', $month->month->i18nFormat('MMMM yyyy'), $month->video_count);
    })->toArray(),
]);
echo $this->Form->end();
