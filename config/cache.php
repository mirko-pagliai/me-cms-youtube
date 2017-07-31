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
if (!defined('ME_CMS_YOUTUBE_CACHE')) {
    define('ME_CMS_YOUTUBE_CACHE', CACHE . 'me_cms_youtube' . DS);
}

return ['Cache' => [
    'videos' => [
        'className' => 'File',
        'duration' => '+999 days',
        'path' => ME_CMS_YOUTUBE_CACHE . 'videos',
        'prefix' => '',
        'mask' => 0777,
    ],
]];
