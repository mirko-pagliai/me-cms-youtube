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
$this->extend('/Videos/index');

if ($date === 'today') {
    $title = __d('me_cms_youtube', 'Videos of today');
} elseif ($date === 'yesterday') {
    $title = __d('me_cms_youtube', 'Videos of yesterday');
} else {
    list($year, $month, $day) = array_replace([null, null, null], explode('/', $date));

    if ($year && $month && $day) {
        $title = __dx('me_cms_youtube', 'videos of day', 'Videos of {0}', $start->i18nFormat(getConfigOrFail('main.date.long')));
    } elseif ($year && $month) {
        $title = __dx('me_cms_youtube', 'videos of month', 'Videos of {0}', $start->i18nFormat('MMMM y'));
    } else {
        $title = __dx('me_cms_youtube', 'videos of year', 'Videos of {0}', $start->i18nFormat('y'));
    }
}

$this->assign('title', $title);

/**
 * Breadcrumb
 */
$this->Breadcrumbs->add($title);
