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
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */
?>

<div class="video-container content-container clearfix">
	<div class="content-header">
		<?php
			if(config('video.show.category') && !empty($video->category->title) && !empty($video->category->slug))
				echo $this->Html->h5($this->Html->link($video->category->title, ['_name' => 'videos_category', $video->category->slug]), ['class' => 'content-category']);

			echo $this->Html->h3($this->Html->link($video->title, ['_name' => 'video', $video->id]), ['class' => 'content-title']);

			if(!empty($video->subtitle))
				echo $this->Html->h4($this->Html->link($video->subtitle, ['_name' => 'video', $video->id]), ['class' => 'content-subtitle']);
		?>
		<div class="content-info">
			<?php
				if(config('video.show.author') && !empty($video->user->full_name))
					echo $this->Html->div('content-author',
						__d('me_cms', 'Posted by {0}', $video->user->full_name),
						['icon' => 'user']
					);

				if(config('video.show.created') && !empty($video->created))
					echo $this->Html->div('content-date',
						__d('me_cms', 'Posted on {0}', $video->created->i18nFormat(config('main.datetime.long'))),
						['icon' => 'clock-o']
					);
			?>
		</div>
	</div>
	<div class="content-text">
		<?php
			//Sets the video source
			$src = sprintf('https://www.youtube.com/embed/%s?modestbranding=1&rel=0&color=white&theme=light', $video->youtube_id);

			//Adds the autoplay
			if(!empty($autoplay))
				$src = sprintf('%s&autoplay=1', $src);

			echo $this->Html->div('embed-responsive embed-responsive-16by9 margin-20', 
				$this->Html->iframe($src, [
					'class'				=> 'embed-responsive-item',
					'allowfullscreen'	=> TRUE,
					'type'				=> 'text/html'
				])
			);

			if(!empty($video->description))
				echo $video->description;
		?>
	</div>
	<?php
		if(!empty(config('video.show.shareaholic')) && !empty(config('shareaholic.app_id')))
			if($this->request->isAction('view') && !$this->request->isAjax())
				echo $this->Html->shareaholic(config('shareaholic.app_id'));
	?>
</div>