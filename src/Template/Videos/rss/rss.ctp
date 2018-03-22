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
use Cake\I18n\I18n;

$this->set([
    'documentData' => ['xmlns:dc' => 'http://purl.org/dc/elements/1.1/'],
    'channelData' => [
        'title' => __d('me_cms_youtube', 'Latest videos'),
        'link' => $this->Url->build('/', true),
        'description' => __d('me_cms_youtube', 'Latest videos'),
        'language' => I18n::getLocale(),
    ],
]);

foreach ($videos as $video) {
    //Sets link
    $link = ['_name' => 'video', $video->id];

    //Sets text
    $text = $this->Text->truncate($video->text, getConfigOrFail('default.truncate_to'), [
        'ending' => '...',
        'exact' => false,
        'html' => true,
    ]);

    //Adds the preview image
    if ($video->has('preview')) {
        $text = $this->Thumb->resize($video->preview->url, ['width' => 400]) . '<br />' . PHP_EOL . $text;
    }

    echo $this->Rss->item([], [
        'description' => $text,
        'guid' => ['url' => $link, 'isPermaLink' => 'true'],
        'link' => $link,
        'pubDate' => $video->created,
        'title' => $video->title,
    ]);
}
