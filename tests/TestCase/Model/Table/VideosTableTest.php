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

use ArrayObject;
use Cake\Cache\Cache;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Reflection\ReflectionTrait;

/**
 * VideosTableTest class
 */
class VideosTableTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var \MeCmsYoutube\Model\Table\VideosTable
     */
    protected $Videos;

    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.me_cms.users',
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

        Cache::clear(false, $this->Videos->cache);
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->Videos);
    }

    /**
     * Test for `cache` property
     * @test
     */
    public function testCacheProperty()
    {
        $this->assertEquals('videos', $this->Videos->cache);
    }

    /**
     * Test for `_getInfo()` method
     * @test
     */
    public function testGetInfo()
    {
        $this->assertFalse($this->invokeMethod($this->Videos, '_getInfo', ['vlSR8Wlmpac']));
    }

    /**
     * Test for `afterDelete()` method
     * @test
     */
    public function testAfterDelete()
    {
        $this->Videos = $this->getMockBuilder(get_class($this->Videos))
            ->setMethods(['setNextToBePublished'])
            ->setConstructorArgs([[
                'table' => $this->Videos->table(),
                'connection' => $this->Videos->getConnection(),
            ]])
            ->getMock();

        $this->Videos->expects($this->once())
            ->method('setNextToBePublished');

        $this->Videos->afterDelete(new Event(null), new Entity, new ArrayObject);
    }

    /**
     * Test for `afterSave()` method
     * @test
     */
    public function testAfterSave()
    {
        $this->Videos = $this->getMockBuilder(get_class($this->Videos))
            ->setMethods(['setNextToBePublished'])
            ->setConstructorArgs([[
                'table' => $this->Videos->table(),
                'connection' => $this->Videos->getConnection(),
            ]])
            ->getMock();

        $this->Videos->expects($this->once())
            ->method('setNextToBePublished');

        $this->Videos->afterSave(new Event(null), new Entity, new ArrayObject);
    }

    /**
     * Test for `beforeSave()` method
     * @test
     */
    public function testBeforeSave()
    {
        $this->Videos = $this->getMockBuilder(get_class($this->Videos))
            ->setMethods(['_getInfo'])
            ->setConstructorArgs([[
                'table' => $this->Videos->table(),
                'connection' => $this->Videos->getConnection(),
            ]])
            ->getMock();

        $this->Videos->expects($this->once())
            ->method('_getInfo')
            ->will($this->returnCallback(function () {
                return (object)[
                    'preview' => 'https://i.ytimg.com/vi/vlSR8Wlmpac/hqdefault.jpg',
                    'text' => 'Example test',
                    'title' => 'Beethoven - Symphony No. 9 in D minor: Ode to Joy [HD]',
                    'seconds' => 778,
                    'duration' => '12:58',
                ];
            }));

        $entity = $this->Videos->newEntity([
            'youtube_id' => 'vlSR8Wlmpac',
            'user_id' => 1,
            'category_id' => 1,
            'title' => 'Example of title',
            'text' => 'Example of text',
        ]);

        $saved = $this->Videos->save($entity);

        $this->assertEquals(778, $saved->seconds);
        $this->assertEquals('12:58', $saved->duration);
    }

    /**
     * Test for `buildRules()` method
     * @test
     */
    public function testBuildRules()
    {
        $entity = $this->Videos->newEntity([
            'youtube_id' => 'vlSR8Wlmpac',
            'user_id' => 999,
            'category_id' => 999,
            'title' => 'My title',
            'text' => 'My text',
        ]);
        $this->assertFalse($this->Videos->save($entity));
        $this->assertEquals([
            'category_id' => ['_existsIn' => 'You have to select a valid option'],
            'user_id' => ['_existsIn' => 'You have to select a valid option'],
        ], $entity->errors());
    }

    /**
     * Test for `initialize()` method
     * @test
     */
    public function testInitialize()
    {
        $this->assertEquals('youtube_videos', $this->Videos->table());
        $this->assertEquals('title', $this->Videos->displayField());
        $this->assertEquals('id', $this->Videos->primaryKey());

        $this->assertInstanceOf('Cake\ORM\Association\BelongsTo', $this->Videos->Categories);
        $this->assertEquals('category_id', $this->Videos->Categories->foreignKey());
        $this->assertEquals('INNER', $this->Videos->Categories->joinType());
        $this->assertEquals('MeCmsYoutube.VideosCategories', $this->Videos->Categories->className());

        $this->assertInstanceOf('Cake\ORM\Association\BelongsTo', $this->Videos->Users);
        $this->assertEquals('user_id', $this->Videos->Users->foreignKey());
        $this->assertEquals('INNER', $this->Videos->Users->joinType());
        $this->assertEquals('MeCms.Users', $this->Videos->Users->className());

        $this->assertTrue($this->Videos->hasBehavior('Timestamp'));
        $this->assertTrue($this->Videos->hasBehavior('CounterCache'));

        $this->assertInstanceOf('MeCmsYoutube\Model\Validation\VideoValidator', $this->Videos->validator());
    }

    /**
     * Test for the `belongsTo` association with `VideosCategories`
     * @test
     */
    public function testBelongsToVideosCategories()
    {
        $entity = $this->Videos->findById(3)->contain(['Categories'])->first();

        $this->assertNotEmpty($entity->category);

        $this->assertInstanceOf('MeCmsYoutube\Model\Entity\VideosCategory', $entity->category);
        $this->assertEquals(4, $entity->category->id);
    }

    /**
     * Test for the `belongsTo` association with `Users`
     * @test
     */
    public function testBelongsToUsers()
    {
        $entity = $this->Videos->findById(2)->contain(['Users'])->first();

        $this->assertNotEmpty($entity->user);

        $this->assertInstanceOf('MeCms\Model\Entity\User', $entity->user);
        $this->assertEquals(3, $entity->user->id);
    }

    /**
     * Test for `find()` method
     * @test
     */
    public function testFind()
    {
        $query = $this->Videos->find();
        $this->assertInstanceOf('Cake\ORM\Query', $query);

        //Writes `next_to_be_published` and some data on cache
        Cache::write('next_to_be_published', time() - 3600, $this->Videos->cache);
        Cache::write('someData', 'someValue', $this->Videos->cache);

        $this->assertNotEmpty(Cache::read('next_to_be_published', $this->Videos->cache));
        $this->assertNotEmpty(Cache::read('someData', $this->Videos->cache));

        //The cache will now be cleared
        $query = $this->Videos->find();
        $this->assertInstanceOf('Cake\ORM\Query', $query);

        $this->assertEmpty(Cache::read('next_to_be_published', $this->Videos->cache));
        $this->assertEmpty(Cache::read('someData', $this->Videos->cache));
    }

    /**
     * Test for `findActive()` method
     * @test
     */
    public function testFindActive()
    {
        $this->assertTrue($this->Videos->hasFinder('active'));

        $query = $this->Videos->find('active');
        $this->assertInstanceOf('Cake\ORM\Query', $query);
        $this->assertEquals('SELECT Videos.id AS `Videos__id`, Videos.user_id AS `Videos__user_id`, Videos.youtube_id AS `Videos__youtube_id`, Videos.category_id AS `Videos__category_id`, Videos.title AS `Videos__title`, Videos.subtitle AS `Videos__subtitle`, Videos.text AS `Videos__text`, Videos.priority AS `Videos__priority`, Videos.active AS `Videos__active`, Videos.is_spot AS `Videos__is_spot`, Videos.seconds AS `Videos__seconds`, Videos.duration AS `Videos__duration`, Videos.created AS `Videos__created`, Videos.modified AS `Videos__modified` FROM youtube_videos Videos WHERE (Videos.active = :c0 AND Videos.is_spot = :c1 AND Videos.created <= :c2)', $query->sql());

        $this->assertTrue($query->valueBinder()->bindings()[':c0']['value']);
        $this->assertFalse($query->valueBinder()->bindings()[':c1']['value']);
        $this->assertInstanceOf('Cake\I18n\Time', $query->valueBinder()->bindings()[':c2']['value']);

        $this->assertNotEmpty($query->count());

        foreach ($query->toArray() as $entity) {
            $this->assertTrue($entity->active);
            $this->assertTrue(!$entity->is_spot);
            $this->assertTrue(!$entity->created->isFuture());
        }
    }

    /**
     * Test for `getRandomSpots()` method
     * @test
     */
    public function testGetRandomSpots()
    {
        $spots = $this->Videos->getRandomSpots();

        $this->assertCount(1, $spots);
        $this->assertInstanceOf('MeCmsYoutube\Model\Entity\Video', $spots[0]);
        $this->assertNotEmpty($spots[0]->youtube_id);

        $spots = $this->Videos->getRandomSpots(2);

        $this->assertCount(2, $spots);

        foreach ($spots as $spot) {
            $this->assertInstanceOf('MeCmsYoutube\Model\Entity\Video', $spot);
            $this->assertNotEmpty($spot->youtube_id);
        }
    }

    /**
     * Test for `queryFromFilter()` method
     * @test
     */
    public function testQueryFromFilter()
    {
        $data = ['spot' => true];

        $query = $this->Videos->queryFromFilter($this->Videos->find(), $data);
        $this->assertInstanceOf('Cake\ORM\Query', $query);
        $this->assertEquals('SELECT Videos.id AS `Videos__id`, Videos.user_id AS `Videos__user_id`, Videos.youtube_id AS `Videos__youtube_id`, Videos.category_id AS `Videos__category_id`, Videos.title AS `Videos__title`, Videos.subtitle AS `Videos__subtitle`, Videos.text AS `Videos__text`, Videos.priority AS `Videos__priority`, Videos.active AS `Videos__active`, Videos.is_spot AS `Videos__is_spot`, Videos.seconds AS `Videos__seconds`, Videos.duration AS `Videos__duration`, Videos.created AS `Videos__created`, Videos.modified AS `Videos__modified` FROM youtube_videos Videos WHERE Videos.is_spot = :c0', $query->sql());

        $this->assertTrue($query->valueBinder()->bindings()[':c0']['value']);
    }
}
