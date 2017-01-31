<?php
/**
 * This file is part of me-cms-youtube.
 *
 * me-cms-youtube is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * me-cms-youtube is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with me-cms-youtube.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */

if (!isset($truncate['title'])) {
    $truncate['title'] = 40;
}

$title = $video->title;

if (isset($truncate['title']) && $truncate['title']) {
    $title = $this->Text->truncate($title, $truncate['title'], ['exact' => false]);
}

if (!empty($video->text)) {
    if (!isset($truncate['text'])) {
        $truncate['text'] = 80;
    }

    $text = strip_tags($video->text);

    if (isset($truncate['text']) && $truncate['text']) {
        $text = $this->Text->truncate($text, $truncate['text'], ['exact' => false]);
    }
}

?>

<div class="content-preview">
    <a href="<?= $this->Url->build(['_name' => 'video', $video->id]) ?>">
        <div>
            <div>
                <div class="content-title"><?= $title ?></div>
                <?php if (!empty($text)) : ?>
                    <div class="content-text"><?= $text ?></div>
                <?php endif; ?>
            </div>
        </div>
        <?= $this->Thumb->fit($video->preview, ['width' => 205], ['alt' => $title]) . PHP_EOL ?>
    </a>
</div>
