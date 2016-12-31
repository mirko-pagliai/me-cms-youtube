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
 * @see         MeCms\Utility\SitemapBuilder
 */
namespace MeCmsYoutube\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * YoutubeVideosCategoriesFixture
 */
class YoutubeVideosCategoriesFixture extends TestFixture
{
    /**
     * Fields
     * @var array
     */
    //@codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'parent_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'lft' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'rght' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'title' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'slug' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'description' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'video_count' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'title' => ['type' => 'unique', 'columns' => ['title'], 'length' => []],
            'slug' => ['type' => 'unique', 'columns' => ['slug'], 'length' => []],
        ],
    ];
    //@codingStandardsIgnoreEnd

    /**
     * Records
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'parent_id' => null,
            'lft' => 1,
            'rght' => 6,
            'title' => 'First video category',
            'slug' => 'first-video-category',
            'description' => 'Description of first category',
            'video_count' => 1,
            'created' => '2016-12-26 21:24:32',
            'modified' => '2016-12-26 21:24:32',
        ],
        [
            'id' => 2,
            'parent_id' => null,
            'lft' => 7,
            'rght' => 8,
            'title' => 'Another video category',
            'slug' => 'another-video-category',
            'description' => 'Description of another category',
            'video_count' => 0,
            'created' => '2016-12-26 21:25:32',
            'modified' => '2016-12-26 21:25:32',
        ],
        [
            'id' => 3,
            'parent_id' => 1,
            'lft' => 2,
            'rght' => 5,
            'title' => 'Sub video category',
            'slug' => 'sub-video-category',
            'description' => 'Description of sub category',
            'video_count' => 1,
            'created' => '2016-12-26 21:26:32',
            'modified' => '2016-12-26 21:26:32',
        ],
        [
            'id' => 4,
            'parent_id' => 3,
            'lft' => 3,
            'rght' => 4,
            'title' => 'Sub sub video category',
            'slug' => 'sub-sub-video-category',
            'description' => 'Description of sub sub category',
            'video_count' => 1,
            'created' => '2016-12-26 21:27:32',
            'modified' => '2016-12-26 21:27:32',
        ],
    ];
}
