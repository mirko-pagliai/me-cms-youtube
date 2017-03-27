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
            $key = config('Youtube.key');
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
    protected function _parseDuration($duration)
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
    protected function _getInfoResponse($id)
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
     * @uses _getInfoResponse()
     * @uses _parseDuration()
     */
    public function getInfo($id)
    {
        $info = json_decode($this->_getInfoResponse($id));

        if (empty($info->items[0])) {
            return false;
        }

        $info = $info->items[0];

        list($seconds, $duration) = $this->_parseDuration($info->contentDetails->duration);

        $object = new \stdClass;
        $object->preview = $info->snippet->thumbnails->high->url;
        $object->text = $info->snippet->description;
        $object->title = $info->snippet->title;
        $object->seconds = $seconds;
        $object->duration = $duration;

        return $object;
    }
}
