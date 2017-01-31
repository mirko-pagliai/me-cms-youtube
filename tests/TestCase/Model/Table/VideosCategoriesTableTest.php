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
use Cake\TestSuite\TestCase;

/**
 * VideosCategoriesTableTest class
 */
class VideosCategoriesTableTest extends TestCase
{
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

        $this->VideosCategories = TableRegistry::get('MeCmsYoutube.VideosCategories');

        Cache::clear(false, $this->VideosCategories->cache);
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->VideosCategories);
    }

    /**
     * Test for `cache` property
     * @test
     */
    public function testCacheProperty()
    {
        $this->assertEquals('videos', $this->VideosCategories->cache);
    }

    /**
     * Test for `buildRules()` method
     * @test
     */
    public function testBuildRules()
    {
        $entity = $this->VideosCategories->newEntity([
            'parent_id' => 999,
            'title' => 'My title',
            'slug' => 'my-slug',
        ]);
        $this->assertFalse($this->VideosCategories->save($entity));
        $this->assertEquals(['parent_id' => ['_existsIn' => 'You have to select a valid option']], $entity->errors());
    }

    /**
     * Test for `initialize()` method
     * @test
     */
    public function testInitialize()
    {
        $this->assertEquals('youtube_videos_categories', $this->VideosCategories->table());
        $this->assertEquals('title', $this->VideosCategories->displayField());
        $this->assertEquals('id', $this->VideosCategories->primaryKey());

        $this->assertEquals('Cake\ORM\Association\BelongsTo', get_class($this->VideosCategories->Parents));
        $this->assertEquals('parent_id', $this->VideosCategories->Parents->foreignKey());
        $this->assertEquals('MeCmsYoutube.VideosCategories', $this->VideosCategories->Parents->className());

        $this->assertEquals('Cake\ORM\Association\HasMany', get_class($this->VideosCategories->Childs));
        $this->assertEquals('parent_id', $this->VideosCategories->Childs->foreignKey());
        $this->assertEquals('MeCmsYoutube.VideosCategories', $this->VideosCategories->Childs->className());

        $this->assertEquals('Cake\ORM\Association\HasMany', get_class($this->VideosCategories->Videos));
        $this->assertEquals('category_id', $this->VideosCategories->Videos->foreignKey());
        $this->assertEquals('MeCmsYoutube.Videos', $this->VideosCategories->Videos->className());

        $this->assertTrue($this->VideosCategories->hasBehavior('Timestamp'));
        $this->assertTrue($this->VideosCategories->hasBehavior('Tree'));
    }

    /**
     * Test for the `belongsTo` association with `VideosCategories` parents
     * @test
     */
    public function testBelongsToParents()
    {
        $category = $this->VideosCategories->findById(4)->contain(['Parents'])->first();

        $this->assertNotEmpty($category->parent);

        $this->assertEquals('MeCmsYoutube\Model\Entity\VideosCategory', get_class($category->parent));
        $this->assertEquals(3, $category->parent->id);

        $category = $this->VideosCategories->findById($category->parent->id)->contain(['Parents'])->first();

        $this->assertEquals('MeCmsYoutube\Model\Entity\VideosCategory', get_class($category->parent));
        $this->assertEquals(1, $category->parent->id);
    }

    /**
     * Test for the `hasMany` association with `VideosCategories` childs
     * @test
     */
    public function testHasManyChilds()
    {
        $category = $this->VideosCategories->findById(1)->contain(['Childs'])->first();

        $this->assertNotEmpty($category->childs);

        foreach ($category->childs as $children) {
            $this->assertEquals('MeCmsYoutube\Model\Entity\VideosCategory', get_class($children));
            $this->assertEquals(1, $children->parent_id);

            $category = $this->VideosCategories->findById($children->id)->contain(['Childs'])->first();

            $this->assertNotEmpty($category->childs);

            foreach ($category->childs as $children) {
                $this->assertEquals('MeCmsYoutube\Model\Entity\VideosCategory', get_class($children));
                $this->assertEquals(3, $children->parent_id);
            }
        }
    }

    /**
     * Test for the `hasMany` association with `Videos`
     * @test
     */
    public function testHasManyPages()
    {
        $category = $this->VideosCategories->findById(4)->contain(['Videos'])->first();

        $this->assertNotEmpty($category->videos);

        foreach ($category->videos as $video) {
            $this->assertEquals('MeCmsYoutube\Model\Entity\Video', get_class($video));
            $this->assertEquals(4, $video->category_id);
        }
    }

    /**
     * Test for `findActive()` method
     * @test
     */
    public function testFindActive()
    {
        $this->assertTrue($this->VideosCategories->hasFinder('active'));

        $query = $this->VideosCategories->find('active');
        $this->assertEquals('Cake\ORM\Query', get_class($query));
        $this->assertEquals('SELECT VideosCategories.id AS `VideosCategories__id`, VideosCategories.parent_id AS `VideosCategories__parent_id`, VideosCategories.lft AS `VideosCategories__lft`, VideosCategories.rght AS `VideosCategories__rght`, VideosCategories.title AS `VideosCategories__title`, VideosCategories.slug AS `VideosCategories__slug`, VideosCategories.description AS `VideosCategories__description`, VideosCategories.video_count AS `VideosCategories__video_count`, VideosCategories.created AS `VideosCategories__created`, VideosCategories.modified AS `VideosCategories__modified` FROM youtube_videos_categories VideosCategories WHERE VideosCategories.video_count > :c0', $query->sql());

        $this->assertEquals(0, $query->valueBinder()->bindings()[':c0']['value']);

        $this->assertNotEmpty($query->count());

        foreach ($query->toArray() as $category) {
            $this->assertNotEquals(0, $category->video_count);
        }
    }

    /**
     * Test for `validationDefault()` method
     * @test
     */
    public function testValidationDefault()
    {
        $this->assertEquals(
            'MeCmsYoutube\Model\Validation\VideosCategoryValidator',
            get_class($this->VideosCategories->validationDefault(new \Cake\Validation\Validator))
        );
    }
}
