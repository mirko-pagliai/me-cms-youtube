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
        $this->assertEquals([
            'parent_id' => ['_existsIn' => I18N_SELECT_VALID_OPTION],
        ], $entity->getErrors());
    }

    /**
     * Test for `initialize()` method
     * @test
     */
    public function testInitialize()
    {
        $this->assertEquals('youtube_videos_categories', $this->VideosCategories->getTable());
        $this->assertEquals('title', $this->VideosCategories->getDisplayField());
        $this->assertEquals('id', $this->VideosCategories->getPrimaryKey());

        $this->assertInstanceOf('Cake\ORM\Association\BelongsTo', $this->VideosCategories->Parents);
        $this->assertEquals('parent_id', $this->VideosCategories->Parents->getForeignKey());
        $this->assertEquals('MeCmsYoutube.VideosCategories', $this->VideosCategories->Parents->className());

        $this->assertInstanceOf('Cake\ORM\Association\HasMany', $this->VideosCategories->Childs);
        $this->assertEquals('parent_id', $this->VideosCategories->Childs->getForeignKey());
        $this->assertEquals('MeCmsYoutube.VideosCategories', $this->VideosCategories->Childs->className());

        $this->assertInstanceOf('Cake\ORM\Association\HasMany', $this->VideosCategories->Videos);
        $this->assertEquals('category_id', $this->VideosCategories->Videos->getForeignKey());
        $this->assertEquals('MeCmsYoutube.Videos', $this->VideosCategories->Videos->className());

        $this->assertTrue($this->VideosCategories->hasBehavior('Timestamp'));
        $this->assertTrue($this->VideosCategories->hasBehavior('Tree'));

        $this->assertInstanceOf('MeCmsYoutube\Model\Validation\VideosCategoryValidator', $this->VideosCategories->validator());
    }

    /**
     * Test for the `belongsTo` association with `VideosCategories` parents
     * @test
     */
    public function testBelongsToParents()
    {
        $category = $this->VideosCategories->findById(4)->contain(['Parents'])->first();

        $this->assertInstanceOf('MeCmsYoutube\Model\Entity\VideosCategory', $category->parent);
        $this->assertEquals(3, $category->parent->id);

        $category = $this->VideosCategories->findById($category->parent->id)->contain(['Parents'])->first();

        $this->assertInstanceOf('MeCmsYoutube\Model\Entity\VideosCategory', $category->parent);
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
            $this->assertInstanceOf('MeCmsYoutube\Model\Entity\VideosCategory', $children);
            $this->assertEquals(1, $children->parent_id);

            $category = $this->VideosCategories->findById($children->id)->contain(['Childs'])->first();

            $this->assertNotEmpty($category->childs);

            foreach ($category->childs as $children) {
                $this->assertInstanceOf('MeCmsYoutube\Model\Entity\VideosCategory', $children);
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
        $category = $this->VideosCategories->find()->contain(['Videos'])->first();

        $this->assertNotEmpty($category->videos);

        foreach ($category->videos as $video) {
            $this->assertInstanceOf('MeCmsYoutube\Model\Entity\Video', $video);
            $this->assertEquals($category->id, $video->category_id);
        }
    }

    /**
     * Test for `findActive()` method
     * @test
     */
    public function testFindActive()
    {
        $query = $this->VideosCategories->find('active');
        $this->assertStringEndsWith('FROM youtube_videos_categories Categories INNER JOIN youtube_videos Videos ON (Videos.active = :c0 AND Videos.created <= :c1 AND Videos.is_spot = :c2 AND Categories.id = (Videos.category_id))', $query->sql());

        $this->assertTrue($query->valueBinder()->bindings()[':c0']['value']);
        $this->assertInstanceOf('Cake\I18n\Time', $query->valueBinder()->bindings()[':c1']['value']);
        $this->assertFalse($query->valueBinder()->bindings()[':c2']['value']);
    }
}
