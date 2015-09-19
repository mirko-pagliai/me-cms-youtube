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

<?php $this->assign('title', __('Edit videos category')); ?>

<div class="youtubeVideosCategories form">
	<?= $this->Html->h2(__('Edit Videos Category')) ?>
    <?= $this->Form->create($youtubeVideosCategory); ?>
    <fieldset>
        <?php
            echo $this->Form->input('parent_id', ['options' => $parentYoutubeVideosCategories, 'empty' => true]);
            echo $this->Form->input('lft');
            echo $this->Form->input('rght');
            echo $this->Form->input('title');
            echo $this->Form->input('slug');
            echo $this->Form->input('description');
            echo $this->Form->input('video_count');
        ?>
    </fieldset>
    <?= $this->Form->submit(__('Edit Videos Category')) ?>
    <?= $this->Form->end() ?>
</div>