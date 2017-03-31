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
namespace MeCmsYoutube\Model\Entity;

use Cake\ORM\Entity;
use MeCmsYoutube\Utility\Youtube;

/**
 * YoutubeVideo entity
 * @property int $id
 * @property int $user_id
 * @property \MeCmsYoutube\Model\Entity\User $user
 * @property string $youtube_id
 * @property \MeCmsYoutube\Model\Entity\Youtube $youtube
 * @property int $category_id
 * @property \MeCmsYoutube\Model\Entity\Category $category
 * @property string $title
 * @property string $subtitle
 * @property string $text
 * @property int $priority
 * @property bool $active
 * @property bool $is_spot
 * @property string $duration
 * @property int $seconds
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 */
class Video extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity()
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'modified' => false,
    ];

    /**
     * Virtual fields that should be exposed
     * @var array
     */
    protected $_virtual = ['preview', 'youtube_url'];

    /**
     * Gets the image preview (virtual field)
     * @return string|null
     * @uses MeCmsYoutube\Utility\Youtube::getPreview()
     */
    protected function _getPreview()
    {
        if (empty($this->_properties['youtube_id'])) {
            return null;
        }

        return [
            'preview' => Youtube::getPreview($this->_properties['youtube_id']),
            'width' => 480,
            'height' => 360,
        ];
    }

    /**
     * Gets the Youtube url (virtual field)
     * @return string|null
     * @uses MeCmsYoutube\Utility\Youtube::getUrl()
     */
    protected function _getYoutubeUrl()
    {
        if (empty($this->_properties['youtube_id'])) {
            return null;
        }

        return Youtube::getUrl($this->_properties['youtube_id']);
    }
}
