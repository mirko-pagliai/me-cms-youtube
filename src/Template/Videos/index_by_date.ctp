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
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */
?>

<?php
    $this->extend('/Videos/index');
    
    $date = new \Cake\I18n\Time();
    $date->year($year)
        ->month(empty($month) ? 1 : $month)
        ->day(empty($day) ? 1 : $day);
    
    if($year && $month && $day) {        
		if($date->isToday()) {
			$title = __d('me_youtube', 'Videos of today');
        }
		elseif($date->isYesterday()) {
			$title = __d('me_youtube', 'Videos of yesterday');
        }
		else {
			$title = __dx('me_youtube', 'videos of day', 'Videos of {0}', $date->i18nFormat(config('main.date.long')));
        }
    }
    elseif($year && $month) {
        $title =__dx('me_youtube', 'videos of month', 'Videos of {0}', $date->i18nFormat('MMMM y'));
    }
    else {
        $title = __dx('me_youtube', 'videos of year', 'Videos of {0}', $date->i18nFormat('y'));
    }
    
    $this->assign('title', $title);
    $this->Breadcrumb->add($title);