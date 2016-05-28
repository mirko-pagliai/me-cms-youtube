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

<?php $this->append('userbar'); ?>
<?php if($video->is_spot): ?>
    <li><?= $this->Html->span(__d('me_youtube', 'Spot'), ['class' => 'label label-primary']) ?></li>
<?php endif; ?>

<?php if(!$video->active): ?>
    <li><?= $this->Html->span(__d('me_cms', 'Draft'), ['class' => 'label label-warning']) ?></li>
<?php endif; ?>

<?php if($video->created->isFuture()): ?>
    <li><?= $this->Html->span(__d('me_cms', 'Scheduled'), ['class' => 'label label-warning']) ?></li>
<?php endif; ?>

<li><?= $this->Html->link(__d('me_youtube', 'Edit video'), ['action' => 'edit', $video->id, 'prefix' => 'admin'], ['icon' => 'pencil', 'target' => '_blank']) ?></li>
<li><?= $this->Form->postLink(__d('me_youtube', 'Delete video'), ['action' => 'delete', $video->id, 'prefix' => 'admin'], ['icon' => 'trash-o', 'confirm' => __d('me_cms', 'Are you sure you want to delete this?'), 'target' => '_blank']) ?></li>
<?php $this->end(); ?>

<?php
	$this->assign('title', $video->title);
	
	//Set some tags
    if($this->request->isAction('view', 'Videos')) {
        $this->Html->meta(['content' => 'article', 'property' => 'og:type']);
        $this->Html->meta(['content' => $video->modified->toUnixString(), 'property' => 'og:updated_time']);

        if(!empty($video->preview)) {
            $this->Html->meta(['href' => $video->preview, 'rel' => 'image_src']);
            $this->Html->meta(['content' => $video->preview, 'property' => 'og:image']);
        }

        if(!empty($video->description)) {
            $this->Html->meta(['content' => $this->Text->truncate($video->description, 100, ['html' => TRUE]), 'property' => 'og:description']);
        }
    }
?>

<div class="videos view">
	<?= $this->element('frontend/views/video', compact('video')); ?>
</div>