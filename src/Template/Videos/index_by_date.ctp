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
$this->extend('/Videos/index');

if ($date === 'today') {
    $title = __d('me_cms_youtube', 'Videos of today');
} elseif ($date === 'yesterday') {
    $title = __d('me_cms_youtube', 'Videos of yesterday');
} else {
    list($year, $month, $day) = array_replace([null, null, null], explode('/', $date));

    if ($year && $month && $day) {
        $title = __dx('me_cms_youtube', 'videos of day', 'Videos of {0}', $start->i18nFormat(config('main.date.long')));
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
