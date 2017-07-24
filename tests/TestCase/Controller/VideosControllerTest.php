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
namespace MeCmsYoutube\Test\TestCase\Controller;

use Cake\Cache\Cache;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;
use MeCmsYoutube\Controller\VideosController;
use MeCms\Core\Plugin;
use MeCms\TestSuite\Traits\AuthMethodsTrait;

/**
 * VideosControllerTest class
 */
class VideosControllerTest extends IntegrationTestCase
{
    use AuthMethodsTrait;

    /**
     * @var \MeCmsYoutube\Controller\VideosController
     */
    protected $Controller;

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

        $this->Controller = new VideosController;
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

        unset($this->Controller, $this->Videos);
    }

    /**
     * Adds additional event spies to the controller/view event manager
     * @param \Cake\Event\Event $event A dispatcher event
     * @param \Cake\Controller\Controller|null $controller Controller instance
     * @return void
     */
    public function controllerSpy($event, $controller = null)
    {
        $controller->Videos->Youtube = $this->getMockBuilder(get_class($controller->Videos->Youtube))
            ->setMethods(['_getInfoResponse'])
            ->getMock();

        $controller->Videos->Youtube->method('_getInfoResponse')
            ->will($this->returnCallback(function () {
                $content = file_get_contents(TEST_APP . 'examples' . DS . 'video.json');
                $content = json_decode($content, true);

                $content['items'][0]['snippet']['thumbnails']['high']['url'] = TEST_APP . 'examples' . DS . 'thumbnail.jpg';

                return json_encode($content);
            }));

        $controller->viewBuilder()->setLayout(false);

        parent::controllerSpy($event, $controller);
    }

    /**
     * Tests for `index()` method
     * @test
     */
    public function testIndex()
    {
        $url = ['_name' => 'videos'];

        $this->get($url);
        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        $this->assertTemplate(ROOT . 'src/Template/Videos/index.ctp');

        $videosFromView = $this->viewVariable('videos');
        $this->assertInstanceof('Cake\ORM\ResultSet', $videosFromView);
        $this->assertNotEmpty($videosFromView);

        foreach ($videosFromView as $video) {
            $this->assertInstanceof('MeCmsYoutube\Model\Entity\Video', $video);
        }

        //Sets the cache name
        $cache = sprintf('index_limit_%s_page_%s', getConfigOrFail('default.records'), 1);
        list($videosFromCache, $pagingFromCache) = array_values(Cache::readMany(
            [$cache, sprintf('%s_paging', $cache)],
            $this->Videos->cache
        ));

        $this->assertEquals($videosFromView->toArray(), $videosFromCache->toArray());
        $this->assertNotEmpty($pagingFromCache['Videos']);

        //GET request again. Now the data is in cache
        $this->get($url);
        $this->assertResponseOk();
        $this->assertNotEmpty($this->_controller->request->getParam('paging')['Videos']);
    }

    /**
     * Tests for `indexByDate()` method
     * @test
     */
    public function testIndexByDate()
    {
        $date = '2016/12/31';
        $url = ['_name' => 'videosByDate', $date];

        $this->get($url);
        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        $this->assertTemplate(ROOT . 'src/Template/Videos/index_by_date.ctp');

        $dateFromView = $this->viewVariable('date');
        $this->assertEquals($date, $dateFromView);

        $videosFromView = $this->viewVariable('videos');
        $this->assertInstanceof('Cake\ORM\ResultSet', $videosFromView);
        $this->assertNotEmpty($videosFromView->toArray());

        foreach ($videosFromView as $video) {
            $this->assertInstanceof('MeCmsYoutube\Model\Entity\Video', $video);
        }

        $startFromView = $this->viewVariable('start');
        $this->assertInstanceof('Cake\I18n\Time', $startFromView);
        $this->assertEquals('2016-12-31 00:00:00', $startFromView->i18nFormat('yyyy-MM-dd HH:mm:ss'));

        //Sets the cache name
        $end = Time::parse($startFromView)->addDay(1);
        $cache = sprintf('index_date_%s_limit_%s_page_%s', md5(serialize([$startFromView, $end])), getConfigOrFail('default.records'), 1);
        list($videosFromCache, $pagingFromCache) = array_values(Cache::readMany(
            [$cache, sprintf('%s_paging', $cache)],
            $this->Videos->cache
        ));

        $this->assertEquals($videosFromView->toArray(), $videosFromCache->toArray());
        $this->assertNotEmpty($pagingFromCache['Videos']);

        //GET request again. Now the data is in cache
        $this->get($url);
        $this->assertResponseOk();
        $this->assertNotEmpty($this->_controller->request->getParam('paging')['Videos']);

        //Tries with various possible dates
        foreach ([
            'today',
            'yesterday',
            '2016',
            '2016/12',
            '2016/12/31',
        ] as $date) {
            $this->get(['_name' => 'videosByDate', $date]);
            $this->assertResponseOk();
            $this->assertResponseNotEmpty();
            $this->assertTemplate(ROOT . 'src/Template/Videos/index_by_date.ctp');
        }

        //GET request with query string
        $this->get(array_merge($url, ['?' => ['q' => $date]]));
        $this->assertRedirect($url);
    }

    /**
     * Tests for `rss()` method
     * @test
     */
    public function testRss()
    {
        $this->get('/videos/rss');
        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        $this->assertTemplate(ROOT . 'src/Template/Videos/rss/rss.ctp');

        $videosFromView = $this->viewVariable('videos');
        $this->assertInstanceof('Cake\ORM\Query', $videosFromView);
        $this->assertNotEmpty($videosFromView->toArray());

        foreach ($videosFromView as $video) {
            $this->assertInstanceof('MeCmsYoutube\Model\Entity\Video', $video);
        }

        $this->assertHeaderContains('Content-Type', 'application/rss+xml');

        $this->assertLayout(Plugin::path(ME_CMS, 'src/Template/Layout/rss/default.ctp'));
    }

    /**
     * Tests for `rss()` method, using an invalid extension
     * @expectedException \Cake\Network\Exception\ForbiddenException
     * @test
     */
    public function testRssInvalidExtension()
    {
        $this->Controller->request = $this->Controller->request->withParam('_ext', 'html');
        $this->Controller->rss();
    }

    /**
     * Tests for `search()` method
     * @test
     */
    public function testSearch()
    {
        $pattern = 'Second video text';
        $url = ['_name' => 'videosSearch'];

        $this->get($url);
        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        $this->assertTemplate(ROOT . 'src/Template/Videos/search.ctp');

        $this->assertEmpty($this->viewVariable('videos'));
        $this->assertEmpty($this->viewVariable('pattern'));

        $this->get(array_merge($url, ['?' => ['p' => $pattern]]));
        $this->assertResponseOk();
        $this->assertResponseNotEmpty();

        $videosFromView = $this->viewVariable('videos');
        $this->assertInstanceof('Cake\ORM\ResultSet', $videosFromView);
        $this->assertNotEmpty($videosFromView->toArray());

        foreach ($videosFromView as $video) {
            $this->assertInstanceof('MeCmsYoutube\Model\Entity\Video', $video);
            $this->assertContains($pattern, $video->text);
        }

        $this->assertEquals($this->viewVariable('pattern'), $pattern);

        //Sets the cache name
        $cache = sprintf('search_%s_limit_%s_page_%s', md5($pattern), getConfigOrFail('default.records_for_searches'), 1);
        list($videosFromCache, $pagingFromCache) = array_values(Cache::readMany(
            [$cache, sprintf('%s_paging', $cache)],
            $this->Videos->cache
        ));

        $this->assertEquals($videosFromView->toArray(), $videosFromCache->toArray());
        $this->assertNotEmpty($pagingFromCache['Videos']);

        //GET request again. Now the data is in cache
        $this->get(array_merge($url, ['?' => ['p' => $pattern]]));
        $this->assertResponseOk();
        $this->assertNotEmpty($this->_controller->request->getParam('paging')['Videos']);

        $this->get(array_merge($url, ['?' => ['p' => 'a']]));
        $this->assertRedirect($url);
        $this->assertSession('You have to search at least a word of 4 characters', 'Flash.flash.0.message');

        $this->session(['last_search' => [
            'id' => md5(time()),
            'time' => time(),
        ]]);

        $this->get(array_merge($url, ['?' => ['p' => $pattern]]));
        $this->assertRedirect($url);
        $this->assertSession('You have to wait 10 seconds to perform a new search', 'Flash.flash.0.message');
    }

    /**
     * Tests for `view()` method
     * @test
     */
    public function testView()
    {
        $id = $this->Videos->find('active')->extract('id')->first();

        $this->get(['_name' => 'video', $id]);
        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        $this->assertTemplate(ROOT . 'src/Template/Videos/view.ctp');

        $videoFromView = $this->viewVariable('video');
        $this->assertInstanceof('MeCmsYoutube\Model\Entity\Video', $videoFromView);

        $cache = Cache::read(sprintf('view_%s', md5($id)), $this->Videos->cache);
        $this->assertEquals($videoFromView, $cache->first());

        $spotFromView = $this->viewVariable('spot');
        $this->assertNotEmpty($spotFromView);
    }

    /**
     * Tests for `preview()` method
     * @test
     */
    public function testPreview()
    {
        $this->setUserGroup('user');

        $id = $this->Videos->find('pending')->extract('id')->first();

        $this->get(['_name' => 'videosPreview', $id]);
        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        $this->assertTemplate(ROOT . 'src/Template/Videos/view.ctp');

        $videoFromView = $this->viewVariable('video');
        $this->assertInstanceof('MeCmsYoutube\Model\Entity\Video', $videoFromView);

        $spotFromView = $this->viewVariable('spot');
        $this->assertNotEmpty($spotFromView);
    }
}
