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
use MeCmsYoutube\Controller\Admin\VideosController;
use MeCms\TestSuite\Traits\AuthMethodsTrait;

/**
 * VideosControllerTest class
 */
class VideosControllerTest extends IntegrationTestCase
{
    use AuthMethodsTrait;

    /**
     * @var \MeCmsYoutube\Controller\Admin\VideosController
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

        $this->Controller = new VideosController;

        $this->Videos = TableRegistry::get('MeCmsYoutube.Videos');

        Cache::clear(false, $this->Videos->cache);
        Cache::clear(false, $this->Videos->Users->cache);

        $this->url = ['controller' => 'Videos', 'prefix' => ADMIN_PREFIX, 'plugin' => ME_CMS_YOUTUBE];
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
     * Tests for `beforeFilter()` method
     * @test
     */
    public function testBeforeFilter()
    {
        foreach (['add', 'edit'] as $action) {
            $this->get(array_merge($this->url, compact('action'), [1]));
            $this->assertResponseOk();
            $this->assertNotEmpty($this->viewVariable('categories'));
            $this->assertNotEmpty($this->viewVariable('users'));
        }

        $this->get(array_merge($this->url, ['action' => 'index']));
        $this->assertResponseOk();
        $this->assertNotEmpty($this->viewVariable('categories'));
        $this->assertNotEmpty($this->viewVariable('users'));
    }

    /**
     * Tests for `beforeFilter()` method, with no categories
     * @test
     */
    public function testBeforeFilterNoCategories()
    {
        //Deletes all categories
        $this->Videos->Categories->deleteAll(['id IS NOT' => null]);

        foreach (['index', 'add', 'edit'] as $action) {
            $this->get(array_merge($this->url, compact('action'), [1]));
            $this->assertRedirect(['controller' => 'VideosCategories', 'action' => 'index']);
            $this->assertSession('You must first create a category', 'Flash.flash.0.message');
        }
    }

    /**
     * Tests for `beforeFilter()` method, with no users
     * @test
     */
    public function testBeforeFilterNoUsers()
    {
        //Deletes all users
        $this->Videos->Users->deleteAll(['id IS NOT' => null]);

        foreach (['index', 'add', 'edit'] as $action) {
            $this->get(array_merge($this->url, compact('action'), [1]));
            $this->assertRedirect(['controller' => 'Users', 'action' => 'index']);
            $this->assertSession('You must first create an user', 'Flash.flash.0.message');
        }
    }

    /**
     * Tests for `isAuthorized()` method
     * @test
     */
    public function testIsAuthorized()
    {
        $this->assertGroupsAreAuthorized([
            'admin' => true,
            'manager' => true,
            'user' => true,
        ]);

        //`edit` and `delete` actions
        foreach (['edit', 'delete'] as $action) {
            $this->Controller = new VideosController;
            $this->Controller->Videos = $this->Videos;
            $this->Controller->request = $this->Controller->request->withParam('action', $action);

            $this->assertGroupsAreAuthorized([
                'admin' => true,
                'manager' => true,
                'user' => false,
            ]);
        }

        //`edit` action, with an user who owns the record
        $this->Controller = new VideosController;
        $this->Controller->Videos = $this->Videos;
        $this->Controller->request = $this->Controller->request
            ->withParam('action', 'edit')
            ->withParam('pass.0', 1);

        $this->assertUsersAreAuthorized([
            1 => true,
            2 => false,
            3 => false,
            4 => false,
        ]);

        $this->Controller->request = $this->Controller->request
            ->withParam('pass.0', 2);

        $this->assertUsersAreAuthorized([
            1 => false,
            2 => false,
            3 => false,
            4 => true,
        ]);
    }
}
