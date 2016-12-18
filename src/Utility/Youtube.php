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
     * API key
     * @var string
     */
    protected $key;

    /**
     * Construct
     * @param string $key API key
     * @uses $key
     */
    public function __construct($key = null)
    {
        if (empty($key)) {
            $key = config('Youtube.key');
        }

        $this->key = $key;
    }

    /**
     * Internal method to get a info response
     * @param string $id Video ID
     * @return mixed The response body
     * @uses $key
     */
    protected function _getInfoResponse($id)
    {
        $url = 'https://www.googleapis.com/youtube/v3/videos?id=' . $id . '&key=' . $this->key . '&part=snippet,contentDetails&fields=items(snippet(title,description,thumbnails(high(url))),contentDetails(duration))';

        return (new Client())->get($url)->body();
    }

    /**
     * Gets information about a video
     * @param string $id Video ID
     * @return mixed Array or `false`
     * @see https://developers.google.com/youtube/v3/getting-started#partial
     * @uses _getInfoResponse()
     */
    public function getInfo($id)
    {
        $info = json_decode($this->_getInfoResponse($id), true);

        if (empty($info['items'][0]['snippet']) || empty($info['items'][0]['contentDetails'])) {
            return false;
        }

        preg_match('/PT(([0-9]+)M)?(([0-9]+)S)?/', $info['items'][0]['contentDetails']['duration'], $matches);

        $mins = empty($matches[2]) ? "00" : sprintf("%02d", $matches[2]);
        $secs = empty($matches[4]) ? "00" : sprintf("%02d", $matches[4]);

        $info = [
            'preview' => $info['items'][0]['snippet']['thumbnails']['high']['url'],
            'text' => $info['items'][0]['snippet']['description'],
            'title' => $info['items'][0]['snippet']['title'],
        ];

        $info['seconds'] = (int)$mins * 60 + (int)$secs;
        $info['duration'] = sprintf('%s:%s', $mins, $secs);

        return $info;
    }
}
