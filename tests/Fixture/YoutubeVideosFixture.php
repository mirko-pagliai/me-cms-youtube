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
namespace MeCmsYoutube\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * YoutubeVideosFixture
 */
class YoutubeVideosFixture extends TestFixture
{
    /**
     * Fields
     * @var array
     */
    //@codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'user_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'youtube_id' => ['type' => 'string', 'length' => 20, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'category_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'title' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'subtitle' => ['type' => 'string', 'length' => 150, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'text' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null],
        'priority' => ['type' => 'integer', 'length' => 1, 'unsigned' => false, 'null' => false, 'default' => '3', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'active' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null],
        'is_spot' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null],
        'seconds' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'duration' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => '00:00', 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'user_id' => ['type' => 'index', 'columns' => ['user_id'], 'length' => []],
            'category_id' => ['type' => 'index', 'columns' => ['category_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
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
            'user_id' => 1,
            'youtube_id' => 't3217H8JppI',
            'category_id' => 4,
            'title' => 'First video title',
            'subtitle' => 'First video subtitle',
            'text' => 'First video text',
            'priority' => 1,
            'active' => 1,
            'is_spot' => 0,
            'seconds' => 3939,
            'duration' => '01:05:39',
            'created' => '2016-11-30 15:05:40',
            'modified' => '2016-11-30 15:05:40',
        ],
        [
            'id' => 2,
            'user_id' => 4,
            'youtube_id' => 'g65oWFMSoK0',
            'category_id' => 1,
            'title' => 'Second video title',
            'subtitle' => 'Second video subtitle',
            'text' => 'Second video text',
            'priority' => 1,
            'active' => 1,
            'is_spot' => 0,
            'seconds' => 651,
            'duration' => '10:51',
            'created' => '2016-12-31 15:06:40',
            'modified' => '2016-12-31 15:06:40',
        ],
        [
            'id' => 3,
            'user_id' => 3,
            'youtube_id' => 'rrVDATvUitA',
            'category_id' => 4,
            'title' => 'Third video title',
            'subtitle' => 'Third video subtitle',
            'text' => 'Third video text',
            'priority' => 1,
            'active' => 1,
            'is_spot' => 1,
            'seconds' => 339,
            'duration' => '05:39',
            'created' => '2016-12-31 15:07:40',
            'modified' => '2016-12-31 15:07:40',
        ],
        [
            'id' => 4,
            'user_id' => 3,
            'youtube_id' => 'GRxofEmo3HA',
            'category_id' => 4,
            'title' => 'Fourth video title',
            'subtitle' => 'Fourth video subtitle',
            'text' => 'Fourth video text',
            'priority' => 1,
            'active' => 1,
            'is_spot' => 1,
            'seconds' => 339,
            'duration' => '05:39',
            'created' => '2016-12-31 15:08:40',
            'modified' => '2016-12-31 15:08:40',
        ],
        [
            'id' => 5,
            'user_id' => 3,
            'youtube_id' => 'rrVDATvUitA',
            'category_id' => 4,
            'title' => 'Pending video title',
            'subtitle' => 'Pending video subtitle',
            'text' => 'Pending video text',
            'priority' => 1,
            'active' => 0,
            'is_spot' => 0,
            'seconds' => 339,
            'duration' => '05:39',
            'created' => '2016-12-31 15:09:40',
            'modified' => '2016-12-31 15:09:40',
        ],
    ];
}
