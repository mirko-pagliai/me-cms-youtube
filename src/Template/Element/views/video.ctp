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
?>
<div class="video-container content-container clearfix">
    <div class="content-header">
        <?php if (getConfig('video.category') && $video->category && $video->category->title && $video->category->slug) : ?>
            <h5 class="content-category">
                <?= $this->Html->link($video->category->title, ['_name' => 'videosCategory', $video->category->slug]) ?>
            </h5>
        <?php endif; ?>

        <h3 class="content-title">
            <?= $this->Html->link($video->title, ['_name' => 'video', $video->id]) ?>
        </h3>

        <?php if ($video->subtitle) : ?>
            <h4 class="content-subtitle">
                <?= $this->Html->link($video->subtitle, ['_name' => 'video', $video->id]) ?>
            </h4>
        <?php endif; ?>

        <div class="content-info">
            <?php
            if (getConfig('video.author')) {
                echo $this->Html->div(
                    'content-author',
                    __d('me_cms', 'Posted by {0}', $video->user->full_name),
                    ['icon' => 'user']
                );
            }

            if (getConfig('video.created')) {
                echo $this->Html->div(
                    'content-date',
                    __d('me_cms', 'Posted on {0}', $video->created->i18nFormat(getConfigOrFail('main.datetime.long'))),
                    ['icon' => 'clock-o']
                );
            }
            ?>
        </div>
    </div>
    <div class="content-text">
        <?php if ($this->request->isAction(['view', 'preview'], 'Videos')) : ?>
            <?= $this->Asset->script('MeCmsYoutube.video', ['block' => 'script_bottom']) ?>
            <div class="embed-responsive embed-responsive-16by9 mb-4 relative">
                <?php
                if (getConfig('video.skip_button')) {
                    echo $this->Html->div(null, __d('me_cms_youtube', 'Skip to the video'), [
                        'data-secs' => getConfigOrFail('video.skip_seconds'),
                        'id' => 'skip-to-video',
                    ]);
                }

                echo $this->Html->div(null, null, [
                    'data-autoplay' => empty($autoplay) && empty($spot) ? '0' : '1',
                    'data-id' => $video->youtube_id,
                    'data-spot' => empty($spot) ? null : $spot,
                    'id' => 'player',
                ]);
                ?>
            </div>
        <?php else : ?>
            <a class="video-fake" href="<?= $this->Url->build(['_name' => 'video', $video->id]) ?>">
                <?= $this->Html->img($video->preview['preview']) ?>
                <?= $this->Html->icon('youtube-play') ?>
            </a>
        <?php endif; ?>
    </div>

    <?php
    if (getConfig('video.shareaholic') && $this->request->isAction('view', 'Videos') && !$this->request->isAjax()) {
        echo $this->Html->shareaholic(getConfigOrFail('shareaholic.app_id'));
    }
    ?>
</div>
