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
namespace MeCmsYoutube\Test\TestCase\Utility;

use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;
use MeCmsYoutube\Utility\Sitemap;
use MeTools\TestSuite\TestCase;

/**
 * SitemapTest class
 */
class SitemapTest extends TestCase
{
    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.me_cms_youtube.youtube_videos',
        'plugin.me_cms_youtube.youtube_videos_categories',
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

        Cache::clearAll();
    }

    /**
     * Test for `videos()` method
     * @test
     */
    public function testVideos()
    {
        $expected = [
            [
                'loc' => 'http://localhost/videos',
                'lastmod' => '2016-12-31T15:09:40+00:00',
                'priority' => '0.5',
            ],
            [
                'loc' => 'http://localhost/videos/categories',
                'priority' => '0.5',
            ],
            [
                'loc' => 'http://localhost/videos/search',
                'priority' => '0.2',
            ],
            [
                'loc' => 'http://localhost/videos/category/first-video-category',
                'lastmod' => '2016-12-31T15:06:40+00:00',
                'priority' => '0.5',
            ],
            [
                'loc' => 'http://localhost/video/2',
                'lastmod' => '2016-12-31T15:06:40+00:00',
                'priority' => '0.5',
            ],
            [
                'loc' => 'http://localhost/videos/category/sub-sub-video-category',
                'lastmod' => '2016-11-30T15:05:40+00:00',
                'priority' => '0.5',
            ],
            [
                'loc' => 'http://localhost/video/1',
                'lastmod' => '2016-11-30T15:05:40+00:00',
                'priority' => '0.5',
            ],
        ];

        $table = TableRegistry::get('MeCmsYoutube.VideosCategories');

        $this->assertEmpty(Cache::read('sitemap', $table->cache));

        $this->assertEquals($expected, Sitemap::videos());

        $this->assertNotEmpty(Cache::read('sitemap', $table->cache));

        $this->assertEquals($expected, Sitemap::videos());
    }

    /**
     * Test for `videos()` method, with no records
     * @test
     */
    public function testVideosNoRecords()
    {
        //Deletes all records
        TableRegistry::get('MeCmsYoutube.VideosCategories')->deleteAll(['id >=' => 1]);

        $this->assertEmpty(Sitemap::videos());
    }
}
