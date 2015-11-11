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

<?= $this->Html->js('MeYoutube.video.min', ['block' => 'script_bottom']) ?>

<div class="video-container content-container clearfix">
	<div class="content-header">
		<?php
			if(config('video.category') && !empty($video->category->title) && !empty($video->category->slug))
				echo $this->Html->h5($this->Html->link($video->category->title, ['_name' => 'videos_category', $video->category->slug]), ['class' => 'content-category']);

			echo $this->Html->h3($this->Html->link($video->title, ['_name' => 'video', $video->id]), ['class' => 'content-title']);

			if(!empty($video->subtitle))
				echo $this->Html->h4($this->Html->link($video->subtitle, ['_name' => 'video', $video->id]), ['class' => 'content-subtitle']);
		?>
		<div class="content-info">
			<?php
				if(config('video.author') && !empty($video->user->full_name))
					echo $this->Html->div('content-author',
						__d('me_cms', 'Posted by {0}', $video->user->full_name),
						['icon' => 'user']
					);

				if(config('video.created') && !empty($video->created))
					echo $this->Html->div('content-date',
						__d('me_cms', 'Posted on {0}', $video->created->i18nFormat(config('main.datetime.long'))),
						['icon' => 'clock-o']
					);
			?>
		</div>
	</div>
	<div class="content-text">
		<div class="embed-responsive embed-responsive-16by9 margin-20 relative">
			<div id="skip-to-video"><?= __d('me_youtube', 'Skip to the video') ?></div>
			<?= $this->Html->div(NULL, '', [
				'data-autoplay'	=> empty($autoplay) && empty($video->spot_id) ? '0' : '1',
				'data-id'		=> $video->youtube_id,
				'data-spot'		=> empty($video->spot_id) ? NULL : $video->spot_id,
				'id'			=> 'player'
			]) ?>
		</div>
	</div>
	<?php
		if(config('video.shareaholic') && config('shareaholic.app_id'))
			if($this->request->isAction('view', 'Videos') && !$this->request->isAjax())
				echo $this->Html->shareaholic(config('shareaholic.app_id'));
	?>
</div>