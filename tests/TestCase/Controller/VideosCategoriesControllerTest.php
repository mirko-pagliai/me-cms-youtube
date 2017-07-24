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
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * VideosCategoriesControllerTest class
 */
class VideosCategoriesControllerTest extends IntegrationTestCase
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
     * Adds additional event spies to the controller/view event manager
     * @param \Cake\Event\Event $event A dispatcher event
     * @param \Cake\Controller\Controller|null $controller Controller instance
     * @return void
     */
    public function controllerSpy($event, $controller = null)
    {
        $controller->viewBuilder()->setLayout('with_flash');

        parent::controllerSpy($event, $controller);
    }

    /**
     * Tests for `index()` method
     * @test
     */
    public function testIndex()
    {
        $this->get(['_name' => 'videosCategories']);
        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        $this->assertTemplate(ROOT . 'src/Template/VideosCategories/index.ctp');

        $categoriesFromView = $this->viewVariable('categories');
        $this->assertInstanceof('Cake\ORM\Query', $categoriesFromView);
        $this->assertNotEmpty($categoriesFromView->toArray());

        foreach ($categoriesFromView as $category) {
            $this->assertInstanceof('MeCmsYoutube\Model\Entity\VideosCategory', $category);
        }

        $cache = Cache::read('categories_index', $this->VideosCategories->cache);
        $this->assertEquals($categoriesFromView->toArray(), $cache->toArray());
    }

    /**
     * Tests for `view()` method
     * @test
     */
    public function testView()
    {
        $slug = $this->VideosCategories->find('active')
            ->order([sprintf('%s.id', $this->VideosCategories->getAlias()) => 'ASC'])
            ->extract('slug')
            ->first();

        $url = ['_name' => 'videosCategory', $slug];

        $this->get($url);
        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        $this->assertTemplate(ROOT . 'src/Template/VideosCategories/view.ctp');

        $categoryFromView = $this->viewVariable('category');
        $this->assertInstanceof('MeCmsYoutube\Model\Entity\VideosCategory', $categoryFromView);

        $videosFromView = $this->viewVariable('videos');
        $this->assertInstanceof('Cake\ORM\ResultSet', $videosFromView);
        $this->assertNotEmpty($videosFromView);

        foreach ($videosFromView as $video) {
            $this->assertInstanceof('MeCmsYoutube\Model\Entity\Video', $video);
        }

        //Sets the cache name
        $cache = sprintf('category_%s_limit_%s_page_%s', md5($slug), getConfigOrFail('default.records'), 1);
        list($videosFromCache, $pagingFromCache) = array_values(Cache::readMany(
            [$cache, sprintf('%s_paging', $cache)],
            $this->VideosCategories->cache
        ));

        $this->assertEquals($videosFromView->toArray(), $videosFromCache->toArray());
        $this->assertNotEmpty($pagingFromCache['Videos']);

        //GET request again. Now the data is in cache
        $this->get($url);
        $this->assertResponseOk();
        $this->assertNotEmpty($this->_controller->request->getParam('paging')['Videos']);

        //GET request with query string
        $this->get(array_merge($url, ['?' => ['q' => $slug]]));
        $this->assertRedirect($url);

        //GET request with a no existing category
        $this->get(['_name' => 'videosCategory', 'no-existing']);
        $this->assertResponseError();
    }
}
