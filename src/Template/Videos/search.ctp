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

$this->extend('MeCms./Common/index');
$this->assign('title', $title = __d('me_youtube', 'Search videos'));

/**
 * Breadcrumb
 */
$this->Breadcrumb->add($title, ['_name' => 'videos_search']);

echo $this->Form->create(null, ['type' => 'get', 'url' => ['_name' => 'videos_search']]);
echo $this->Form->input('p', [
    'default' => $this->request->query('p'),
    'label' => false,
    'placeholder' => sprintf('%s...', __d('me_cms', 'Search')),
]);
echo $this->Form->submit(__d('me_cms', 'Search'), [
    'class' => 'btn-primary visible-lg-inline',
    'icon' => 'search',
]);
echo $this->Form->end();
?>

<?php if (!empty($pattern)) : ?>
    <div class="bg-info margin-20 padding-10">
        <?= __d('me_cms', 'You have searched for: {0}', $this->Html->em($pattern)) ?>
    </div>
<?php endif; ?>

<?php if (!empty($videos)) : ?>
    <div class="as-table">
        <?php foreach ($videos as $video) : ?>
            <div class="margin-10 padding-10">
                <?= $this->Html->link($video->title, ['_name' => 'video', $video->id]) ?> 
                <span class="small text-muted">(<?= $video->created->i18nFormat(config('main.datetime.short')) ?>)</span>
                <div class="text-justify">
                    <?php
                        echo $this->Text->truncate(
                            strip_tags($video->text),
                            350,
                            ['exact' => false, 'html' => true]
                        );
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?= $this->element('MeTools.paginator') ?>
<?php endif; ?>
