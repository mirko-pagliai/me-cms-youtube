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
	/**
	 * This view can be used by many actions
	 */
	//VideosCategories `view`
	if($this->request->isAction('view', 'VideosCategories') && !empty($videos[0]->category->title))
		$title = $videos[0]->category->title;
	//Videos `index_by_date`
	elseif($this->request->isAction('index_by_date', 'Videos')) {
		$date = (new \Cake\I18n\Time())->year($this->request->param('year'))->month($this->request->param('month'))->day($this->request->param('day'));
		
		if($date->isToday())
			$title = __d('me_youtube', 'Videos of today');
		elseif($date->isYesterday())
			$title = __d('me_youtube', 'Videos of yesterday');
		else
			$title = __d('me_youtube', 'Videos of {0}', $date->i18nFormat(config('main.date.long')));
	}

	if(!empty($title))
		$this->assign('title', $title);
	else
		$this->assign('title', __d('me_youtube', 'Videos'));
?>

<div class="videos index">
	<?php
		if(!empty($title))
			echo $this->Html->h2($title);
		
		if(!empty($videos)) {
			foreach($videos as $video)
				echo $this->element('frontend'.DS.'views'.DS.'video', compact('video'));

			echo $this->element('MeTools.paginator');
		}
	?>
</div>