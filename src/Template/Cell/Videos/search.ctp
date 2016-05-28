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
	//Returns on search
	if($this->request->isHere(['_name' => 'videos_search'])) {
		return;
    }
		
	//Extends the widget common view
	$this->extend('MeCms./Common/widget');
	$this->assign('title', __d('me_youtube', 'Search videos'));

	echo $this->Form->createInline(FALSE, ['type' => 'get', 'url' => ['_name' => 'videos_search']]);
	echo $this->Form->input('p', ['placeholder' => sprintf('%s...', __d('me_cms', 'Search'))]);
	echo $this->Form->submit(NULL, ['class' => 'btn-primary visible-lg-inline', 'icon' => 'search']);
	echo $this->Form->end();
?>