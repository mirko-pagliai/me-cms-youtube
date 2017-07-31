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
