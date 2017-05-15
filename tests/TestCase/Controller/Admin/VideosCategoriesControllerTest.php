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
     * Adds additional event spies to the controller/view event manager
     * @param \Cake\Event\Event $event A dispatcher event
     * @param \Cake\Controller\Controller|null $controller Controller instance
     * @return void
     */
    public function controllerSpy($event, $controller = null)
    {
        $controller->viewBuilder()->setLayout(false);

        parent::controllerSpy($event, $controller);
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
}
