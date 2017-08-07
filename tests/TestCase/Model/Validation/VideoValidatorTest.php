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
namespace MeCmsYoutube\Test\TestCase\Model\Validation;

use Cake\ORM\TableRegistry;
use MeCms\TestSuite\ValidationTestCase;

/**
 * VideoValidatorTest class
 */
class VideoValidatorTest extends ValidationTestCase
{
    /**
     * @var \MeCmsYoutube\Model\Table\VideosTable
     */
    protected $Videos;

    /**
     * Example data
     * @var array
     */
    protected $example;

    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.me_cms_youtube.youtube_videos',
    ];

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Videos = TableRegistry::get('MeCmsYoutube.Videos');

        $this->example = [
            'youtube_id' => 'vlSR8Wlmpac',
            'user_id' => 1,
            'category_id' => 1,
            'title' => 'Example of title',
            'text' => 'Example of text',
        ];
    }

    /**
     * Test validation.
     * It tests the proper functioning of the example data.
     * @test
     */
    public function testValidationExampleData()
    {
        $this->assertAllDataAreRequired($this->Videos, $this->example);
    }

    /**
     * Test validation for `youtube_id` property
     * @test
     */
    public function testValidationForYoutubeId()
    {
        foreach ([
            str_repeat('a', 10),
            str_repeat('a', 12),
            str_repeat('a', 10) . '?',
            str_repeat('a', 10) . '$',
        ] as $value) {
            $this->example['youtube_id'] = $value;
            $this->assertEquals([
                'youtube_id' => ['validYoutubeId' => 'You have to enter a valid YouTube ID'],
            ], $this->Videos->newEntity($this->example)->getErrors());
        }

        foreach ([
            str_repeat('a', 11),
            str_repeat('A', 11),
            str_repeat('1', 11),
            str_repeat('a', 10) . '-',
            str_repeat('a', 10) . '_',
        ] as $value) {
            $this->example['youtube_id'] = $value;
            $this->assertEmpty($this->Videos->newEntity($this->example)->getErrors());
        }
    }

    /**
     * Test validation for `category_id` property
     * @test
     */
    public function testValidationForCategoryId()
    {
        $this->example['category_id'] = 'string';
        $this->assertEquals([
            'category_id' => ['naturalNumber' => I18N_SELECT_VALID_OPTION],
        ], $this->Videos->newEntity($this->example)->getErrors());
    }

    /**
     * Test validation for `is_spot` property
     * @test
     */
    public function testValidationForIsSpot()
    {
        $this->example['is_spot'] = 'string';
        $this->assertEquals([
            'is_spot' => ['boolean' => I18N_SELECT_VALID_OPTION],
        ], $this->Videos->newEntity($this->example)->getErrors());

        foreach ([true, false] as $value) {
            $this->example['is_spot'] = $value;
            $this->assertEmpty($this->Videos->newEntity($this->example)->getErrors());
        }
    }

    /**
     * Test validation for `seconds` property
     * @test
     */
    public function testForSeconds()
    {
        foreach ([0, 'string'] as $value) {
            $this->example['seconds'] = $value;
            $this->assertEquals([
                'seconds' => ['naturalNumber' => I18N_ENTER_VALID_VALUE],
            ], $this->Videos->newEntity($this->example)->getErrors());
        }

        foreach ([1, 100, 1000] as $value) {
            $this->example['seconds'] = $value;
            $this->assertEmpty($this->Videos->newEntity($this->example)->getErrors());
        }
    }

    /**
     * Test validation for `duration` property
     * @test
     */
    public function testForDuration()
    {
        foreach (['00', '1234', '12:3456', 'string'] as $value) {
            $this->example['duration'] = $value;
            $this->assertEquals([
                'duration' => ['validDuration' => I18N_ENTER_VALID_VALUE],
            ], $this->Videos->newEntity($this->example)->getErrors());
        }

        foreach (['00:00', '11:34', '00:00:00', '06:54:34'] as $value) {
            $this->example['duration'] = $value;
            $this->assertEmpty($this->Videos->newEntity($this->example)->getErrors());
        }
    }
}
