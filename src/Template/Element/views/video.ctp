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
$this->Asset->css('MeCmsYoutube.video');

if ($this->request->isAction(['view', 'preview'], 'Videos')) {
    $this->Asset->script('MeCmsYoutube.video', ['block' => 'script_bottom']);
}
?>

<article class="clearfix mb-4">
    <header class="mb-3">
        <?php if (getConfig('video.category') && $video->category->has(['slug', 'title'])) : ?>
            <h5 class="category mb-1">
                <?= $this->Html->link($video->category->title, ['_name' => 'videosCategory', $video->category->slug]) ?>
            </h5>
        <?php endif; ?>

        <h2 class="title mb-1">
            <?= $this->Html->link($video->title, ['_name' => 'video', $video->id]) ?>
        </h2>

        <?php if ($video->has('subtitle')) : ?>
            <h4 class="subtitle mb-1">
                <?= $this->Html->link($video->subtitle, ['_name' => 'video', $video->id]) ?>
            </h4>
        <?php endif; ?>

        <div class="info">
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
                    __d('me_cms', 'Posted on {0}', $video->created->i18nFormat()),
                    ['icon' => 'clock-o']
                );
            }
            ?>
        </div>
    </header>

    <main>
        <div class="embed-responsive embed-responsive-16by9">
        <?php if ($this->request->isAction(['view', 'preview'], 'Videos')) : ?>
            <div class="mb-4 relative">
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
            <a class="embed-responsive-item video-fake" href="<?= $this->Url->build(['_name' => 'video', $video->id]) ?>">
                <?= $this->Html->img($video->preview->url, ['class' => 'embed-responsive-item']) ?>
                <?= $this->Html->icon('youtube-play') ?>
            </a>
        <?php endif; ?>
        </div>
    </main>

    <?php
    if (getConfig('video.shareaholic') && $this->request->isAction('view', 'Videos') && !$this->request->isAjax()) {
        echo $this->Html->shareaholic(getConfigOrFail('shareaholic.app_id'));
    }
    ?>
</article>
