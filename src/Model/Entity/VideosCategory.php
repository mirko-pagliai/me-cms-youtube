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

/**
 * YoutubeVideosCategory entity
 * @property int $id
 * @property int $parent_id
 * @property \MeCmsYoutube\Model\Entity\ParentVideosCategory $parent_videos_category
 * @property int $lft
 * @property int $rght
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property int $video_count
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \MeCmsYoutube\Model\Entity\ChildVideosCategory[] $child_videos_categories
 */
class VideosCategory extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity()
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'video_count' => false,
        'modified' => false,
    ];
}
