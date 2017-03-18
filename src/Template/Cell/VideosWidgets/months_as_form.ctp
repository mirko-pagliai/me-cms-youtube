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
if (empty($months) || count($months) < 2) {
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
    'options' => array_map(function ($month) {
        return sprintf('%s (%s)', $month->month->i18nFormat('MMMM yyyy'), $month->video_count);
    }, $months),
]);
echo $this->Form->end();
