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
namespace MeCmsYoutube\Test\TestCase\Model\Table;

use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;
use MeTools\TestSuite\TestCase;

/**
 * AppTableTest class
 */
class AppTableTest extends TestCase
{
    /**
     * @var \MeCmsYoutube\Model\Table\VideosTable
     */
    protected $Videos;

    /**
     * @var \MeCmsYoutube\Model\Table\VideosCategoriesTable
     */
    protected $VideosCategories;

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

        $this->Videos = TableRegistry::get('MeCmsYoutube.Videos');
        $this->VideosCategories = TableRegistry::get('MeCmsYoutube.VideosCategories');

        Cache::clear(false, $this->Videos->cache);
    }

    /**
     * Test for `getList()` method
     * @test
     */
    public function testGetList()
    {
        $query = $this->VideosCategories->getList();
        $this->assertContains('ORDER BY ' . $this->VideosCategories->getDisplayField() . ' ASC', $query->sql());

        $list = $query->toArray();
        $this->assertEquals([
            2 => 'Another video category',
            1 => 'First video category',
            4 => 'Sub sub video category',
            3 => 'Sub video category',
        ], $list);

        $fromCache = Cache::read('youtube_videos_categories_list', $this->VideosCategories->cache)->toArray();
        $this->assertEquals($list, $fromCache);
    }

    /**
     * Test for `getTreeList()` method
     * @test
     */
    public function testGetTreeList()
    {
        $query = $this->VideosCategories->getTreeList();

        $list = $query->toArray();
        $this->assertEquals([
            1 => 'First video category',
            3 => '—Sub video category',
            4 => '——Sub sub video category',
            2 => 'Another video category',
        ], $list);

        $fromCache = Cache::read('youtube_videos_categories_tree_list', $this->VideosCategories->cache)->toArray();
        $this->assertEquals($list, $fromCache);
    }
}
