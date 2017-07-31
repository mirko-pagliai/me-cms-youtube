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
 * @see         MeTools\Utility\Youtube
 */
namespace MeCmsYoutube\Utility;

use Cake\Http\Client;
use MeTools\Utility\Youtube as BaseYoutube;

/**
 * An utility to get information about YouTube videos
 */
class Youtube extends BaseYoutube
{
    /**
     * @var Cake\Http\Client
     */
    protected $Client;

    /**
     * API key
     * @var string
     */
    protected $key;

    /**
     * Construct
     * @param string $key API key
     * @uses $Client
     * @uses $key
     */
    public function __construct($key = null)
    {
        $this->Client = new Client;

        if (empty($key)) {
            $key = getConfigOrFail('Youtube.key');
        }

        $this->key = $key;
    }

    /**
     * Internal method to parse the duration of a video.
     *
     * It gets the duration in YouTube format (eg. `PT3M5S`) and returns an
     *  array with seconds and the duration in a readable format (eg. `03:05`).
     * @param string $duration Duration in YouTube format
     * @return bool|array Array with second and duration string or `false`
     */
    protected function parseDuration($duration)
    {
        if (!preg_match('/^PT((\d+)H)?((\d+)M)?((\d+)S)?$/', $duration, $matches)) {
            return false;
        }

        $duration = '';
        $hours = $minutes = $seconds = 0;

        if (!empty($matches[2])) {
            $hours = $matches[2];
            $duration = sprintf("%02d", $matches[2]) . ':';
        }

        if (!empty($matches[4])) {
            $minutes = $matches[4];
        }

        if (!empty($matches[6])) {
            $seconds = $matches[6];
        }

        $duration .= sprintf("%02d", $minutes) . ':' . sprintf("%02d", $seconds);

        $seconds = $hours * 3600 + $minutes * 60 + $seconds;

        return [$seconds, $duration];
    }

    /**
     * Internal method to get a info response
     * @param string $id Video ID
     * @return mixed The response body
     * @uses $Client
     * @uses $key
     */
    protected function getInfoResponse($id)
    {
        $url = 'https://www.googleapis.com/youtube/v3/videos?id=' . $id . '&key=' . $this->key .
            '&part=snippet,contentDetails&fields=items(snippet(title,description,thumbnails(high(url))),contentDetails(duration))';

        return $this->Client->get($url)->body;
    }

    /**
     * Gets information about a video
     * @param string $id Video ID
     * @return mixed Object or `false`
     * @see https://developers.google.com/youtube/v3/getting-started#partial
     * @uses getInfoResponse()
     * @uses parseDuration()
     */
    public function getInfo($id)
    {
        $info = json_decode($this->getInfoResponse($id));

        if (empty($info->items[0])) {
            return false;
        }

        $info = $info->items[0];

        list($seconds, $duration) = $this->parseDuration($info->contentDetails->duration);

        $object = new \stdClass;
        $object->preview = $info->snippet->thumbnails->high->url;
        $object->text = $info->snippet->description;
        $object->title = $info->snippet->title;
        $object->seconds = $seconds;
        $object->duration = $duration;

        return $object;
    }
}
