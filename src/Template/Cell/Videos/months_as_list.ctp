<?php
/**
 * This file is part of MeYoutube.
 *
 * MeYoutube is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeYoutube is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeYoutube.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */

if (empty($months) || count($months) < 2) {
    return;
}

$this->extend('MeCms./Common/widget');
$this->assign('title', __d('me_youtube', 'Videos by month'));

$months = array_map(function ($month) {
    return $this->Html->link(
        $month->month->i18nFormat('MMMM Y'),
        [
            '_name' => 'videosByDate',
            sprintf(
                '%s/%s',
                $month->month->i18nFormat('YYYY'),
                $month->month->i18nFormat('MM')
            ),
        ]
    );
}, $months);

echo $this->Html->ul($months, ['icon' => 'caret-right']);
