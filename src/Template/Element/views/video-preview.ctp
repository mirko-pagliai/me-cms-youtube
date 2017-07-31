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
        <?= $this->Thumb->fit($video->preview['preview'], ['width' => 205], ['alt' => $title]) . PHP_EOL ?>
    </a>
</div>
