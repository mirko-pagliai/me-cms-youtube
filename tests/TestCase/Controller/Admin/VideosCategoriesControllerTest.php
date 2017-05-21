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
namespace MeCmsYoutube\Test\TestCase\Admin\Controller;

use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;
use MeCmsYoutube\Controller\Admin\VideosCategoriesController;
use MeCms\TestSuite\Traits\AuthMethodsTrait;

/**
 * VideosCategoriesControllerTest class
 */
class VideosCategoriesControllerTest extends IntegrationTestCase
{
    use AuthMethodsTrait;

    /**
     * @var \MeCmsYoutube\Controller\Admin\VideosCategoriesController
     */
    protected $Controller;

    /**
     * @var \MeCmsYoutube\Model\Table\PostsCategoriesTable
     */
    protected $VideosCategories;

    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.me_cms_youtube.youtube_videos_categories',
    ];

    /**
     * @var array
     */
    protected $url;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->setUserGroup('admin');

        $this->Controller = new VideosCategoriesController;

        $this->VideosCategories = TableRegistry::get('MeCmsYoutube.VideosCategories');

        Cache::clear(false, $this->VideosCategories->cache);

        $this->url = ['controller' => 'VideosCategories', 'prefix' => ADMIN_PREFIX, 'plugin' => ME_CMS_YOUTUBE];
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->Controller, $this->VideosCategories);
    }

    /**
     * Tests for `beforeFilter()` method
     * @test
     */
    public function testBeforeFilter()
    {
        foreach (['add', 'edit'] as $action) {
            $this->get(array_merge($this->url, compact('action'), [1]));
            $this->assertResponseOk();
            $this->assertNotEmpty($this->viewVariable('categories'));
        }
    }

    /**
     * Tests for `isAuthorized()` method
     * @test
     */
    public function testIsAuthorized()
    {
        $this->Controller->request = $this->Controller->request->withParam('prefix', ADMIN_PREFIX);

        $this->assertGroupsAreAuthorized([
            'admin' => true,
            'manager' => true,
            'user' => false,
        ]);

        //`delete` action
        $this->Controller = new VideosCategoriesController;
        $this->Controller->request = $this->Controller->request->withParam('prefix', ADMIN_PREFIX);
        $this->Controller->request = $this->Controller->request->withParam('action', 'delete');

        $this->assertGroupsAreAuthorized([
            'admin' => true,
            'manager' => false,
            'user' => false,
        ]);
    }

    /**
     * Tests for `index()` method
     * @test
     */
    public function testIndex()
    {
        $this->get(array_merge($this->url, ['action' => 'index']));
        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        $this->assertTemplate(ROOT . 'src/Template/Admin/VideosCategories/index.ctp');

        $categoriesFromView = $this->viewVariable('categories');
        $this->assertInstanceof('Cake\ORM\Query', $categoriesFromView);
        $this->assertNotEmpty($categoriesFromView);

        foreach ($categoriesFromView as $category) {
            $this->assertInstanceof('MeCmsYoutube\Model\Entity\VideosCategory', $category);
        }
    }

    /**
     * Tests for `add()` method
     * @test
     */
    public function testAdd()
    {
        $url = array_merge($this->url, ['action' => 'add']);

        $this->get($url);
        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        $this->assertTemplate(ROOT . 'src/Template/Admin/VideosCategories/add.ctp');

        $categoryFromView = $this->viewVariable('category');
        $this->assertInstanceof('MeCmsYoutube\Model\Entity\VideosCategory', $categoryFromView);
        $this->assertNotEmpty($categoryFromView);

        //POST request. Data are valid
        $this->post($url, [
            'title' => 'new category',
            'slug' => 'new-category-slug',
        ]);
        $this->assertRedirect(['action' => 'index']);
        $this->assertSession('The operation has been performed correctly', 'Flash.flash.0.message');

        //POST request. Data are invalid
        $this->post($url, ['title' => 'aa']);
        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        $this->assertResponseContains('The operation has not been performed correctly');

        $categoryFromView = $this->viewVariable('category');
        $this->assertInstanceof('MeCmsYoutube\Model\Entity\VideosCategory', $categoryFromView);
        $this->assertNotEmpty($categoryFromView);
    }

    /**
     * Tests for `edit()` method
     * @test
     */
    public function testEdit()
    {
        $url = array_merge($this->url, ['action' => 'edit', 1]);

        $this->get($url);
        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        $this->assertTemplate(ROOT . 'src/Template/Admin/VideosCategories/edit.ctp');

        $categoryFromView = $this->viewVariable('category');
        $this->assertInstanceof('MeCmsYoutube\Model\Entity\VideosCategory', $categoryFromView);
        $this->assertNotEmpty($categoryFromView);

        //POST request. Data are valid
        $this->post($url, ['title' => 'another title']);
        $this->assertRedirect(['action' => 'index']);
        $this->assertSession('The operation has been performed correctly', 'Flash.flash.0.message');

        //POST request. Data are invalid
        $this->post($url, ['title' => 'aa']);
        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        $this->assertResponseContains('The operation has not been performed correctly');

        $categoryFromView = $this->viewVariable('category');
        $this->assertInstanceof('MeCmsYoutube\Model\Entity\VideosCategory', $categoryFromView);
        $this->assertNotEmpty($categoryFromView);
    }

    /**
     * Tests for `delete()` method
     * @test
     */
    public function testDelete()
    {
        $id = $this->VideosCategories->find()->where(['video_count <' => 1])->extract('id')->first();

        //POST request. This category has no pages
        $this->post(array_merge($this->url, ['action' => 'delete', $id]));
        $this->assertRedirect(['action' => 'index']);
        $this->assertSession('The operation has been performed correctly', 'Flash.flash.0.message');

        $id = $this->VideosCategories->find()->where(['video_count >=' => 1])->extract('id')->first();

        //POST request. This category has some pages, so it cannot be deleted
        $this->post(array_merge($this->url, ['action' => 'delete', $id]));
        $this->assertRedirect(['action' => 'index']);
        $this->assertSession(
            'Before deleting this, you must delete or reassign all items that belong to this element',
            'Flash.flash.0.message'
        );
    }
}
