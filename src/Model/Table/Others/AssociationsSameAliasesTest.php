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
namespace MeCmsYoutube\Model\Table\Others;

use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * AssociationsSameAliasesTest class
 */
class AssociationsSameAliasesTest extends TestCase
{
    /**
     * @var \MeCms\Model\Table\PostsTable
     */
    protected $Posts;

    /**
     * @var \MeCmsYoutube\Model\Table\VideosTable
     */
    protected $Videos;

    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.me_cms.posts',
        'plugin.me_cms.posts_categories',
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

        $this->Posts = TableRegistry::get('MeCms.Posts');
        $this->Videos = TableRegistry::get('MeCmsYoutube.Videos');

        Cache::clearAll();
        Cache::clear(false, $this->Posts->cache);
        Cache::clear(false, $this->Videos->cache);
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->Posts, $this->Videos);
    }

    /**
     * Test for associations with the same alias
     * @test
     */
    public function testAssociationsSameAliases()
    {
        foreach (['Posts', 'Videos'] as $table) {
            $categories = $this->$table->Categories;

            $this->assertInstanceOf('Cake\ORM\Association\BelongsTo', $categories);
            $this->assertEquals('Categories', $categories->getName());

            if ($table === 'Posts') {
                $this->assertEquals('MeCms.' . $table . 'Categories', $categories->className());
            } else {
                $this->assertEquals('MeCmsYoutube.' . $table . 'Categories', $categories->className());
            }

            $category = $categories->find()->first();
            $this->assertNotEmpty($category);

            if ($table === 'Posts') {
                $this->assertInstanceof('MeCms\Model\Entity\\' . $table . 'Category', $category);
            } else {
                $this->assertInstanceof('MeCmsYoutube\Model\Entity\\' . $table . 'Category', $category);
            }
        }
    }
}
