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

<div class="video-container content-container clearfix">
	<div class="content-header">
        <?php if(config('video.category')): ?>
            <h5 class="content-category">
                <?= $this->Html->link($video->category->title, ['_name' => 'videos_category', $video->category->slug]) ?>
            </h5>
        <?php endif; ?>
        
        <h3 class="content-title">
            <?= $this->Html->link($video->title, ['_name' => 'video', $video->id]) ?>
        </h3>
        
        <?php if($video->subtitle): ?>
            <h4 class="content-subtitle">
                <?= $this->Html->link($video->subtitle, ['_name' => 'video', $video->id]) ?>
            </h4>
        <?php endif; ?>
        
		<div class="content-info">
			<?php if(config('video.author')): ?>
				<?= $this->Html->div('content-author', __d('me_cms', 'Posted by {0}', $video->user->full_name), ['icon' => 'user']) ?>
            <?php endif; ?>
            
            <?php if(config('video.created')): ?>
				<?= $this->Html->div('content-date', __d('me_cms', 'Posted on {0}', $video->created->i18nFormat(config('main.datetime.long'))), ['icon' => 'clock-o']) ?>
            <?php endif; ?>
		</div>
	</div>
	<div class="content-text">
		<?php if($this->request->isAction(['view', 'preview'], 'Videos')): ?>
			<?= $this->Asset->js('MeYoutube.video', ['block' => 'script_bottom']) ?>
			<div class="embed-responsive embed-responsive-16by9 margin-20 relative">
				<?php
					if(config('video.skip_button')) {
						echo $this->Html->div(NULL, __d('me_youtube', 'Skip to the video'), [
							'data-secs' => config('video.skip_seconds'),
							'id' => 'skip-to-video',
						]);
                    }
                    
					echo $this->Html->div(NULL, '', [
						'data-autoplay'	=> empty($autoplay) && empty($video->spot_id) ? '0' : '1',
						'data-id' => $video->youtube_id,
						'data-spot' => empty($video->spot_id) ? NULL : $video->spot_id,
						'id' => 'player',
					]);
				?>
			</div>
		<?php else: ?>
			<a class="video-fake" href="<?= $this->Url->build(['_name' => 'video', $video->id]) ?>">
				<?= $this->Html->img($video->preview) ?>
				<?= $this->Html->icon('youtube-play') ?>
			</a>
		<?php endif; ?>
	</div>
	<?php
		if(config('video.shareaholic') && config('shareaholic.app_id') && $this->request->isAction('view', 'Videos') && !$this->request->isAjax()) {
			echo $this->Html->shareaholic(config('shareaholic.app_id'));
        }
	?>
</div>