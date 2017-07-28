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
use MeCmsYoutube\Controller\Admin\VideosController;
use MeCms\TestSuite\IntegrationTestCase;

/**
 * VideosControllerTest class
 */
class VideosControllerTest extends IntegrationTestCase
{
    /**
     * @var \MeCmsYoutube\Controller\Admin\VideosController
     */
    protected $Controller;

    /**
     * @var \MeCmsYoutube\Model\Table\VideosTable
     */
    protected $Videos;

    /**
     * @var array
     */
    protected $example;

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

        $this->example = [
            'user_id' => 1,
            'category_id' => 1,
            'title' => 'new video title',
            'text' => 'new video text',
        ];

        $this->Controller = new VideosController;

        $this->Videos = TableRegistry::get('MeCmsYoutube.Videos');

        Cache::clear(false, $this->Videos->cache);
        Cache::clear(false, $this->Videos->Users->cache);

        $this->url = ['controller' => 'Videos', 'prefix' => ADMIN_PREFIX, 'plugin' => ME_CMS_YOUTUBE];
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
     * Tests for `beforeFilter()` method
     * @test
     */
    public function testBeforeFilter()
    {
        foreach (['add', 'edit', 'index'] as $action) {
            $this->get(array_merge($this->url, compact('action'), [1]));
            $this->assertNotEmpty($this->viewVariable('categories'));
            $this->assertNotEmpty($this->viewVariable('users'));
        }
    }

    /**
     * Tests for `beforeFilter()` method, with no categories
     * @test
     */
    public function testBeforeFilterNoCategories()
    {
        //Deletes all categories
        $this->Videos->Categories->deleteAll(['id IS NOT' => null]);

        foreach (['add', 'edit', 'index'] as $action) {
            $this->get(array_merge($this->url, compact('action'), [1]));
            $this->assertRedirect(['controller' => 'VideosCategories', 'action' => 'index']);
            $this->assertFlashMessage('You must first create a category');
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

        foreach (['add', 'edit', 'index'] as $action) {
            $this->get(array_merge($this->url, compact('action'), [1]));
            $this->assertRedirect(['controller' => 'Users', 'action' => 'index']);
            $this->assertFlashMessage('You must first create an user');
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

    /**
     * Tests for `index()` method
     * @test
     */
    public function testIndex()
    {
        $this->get(array_merge($this->url, ['action' => 'index']));
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate(ROOT . 'src/Template/Admin/Videos/index.ctp');

        $videosFromView = $this->viewVariable('videos');
        $this->assertNotEmpty($videosFromView);
        $this->assertInstanceof('MeCmsYoutube\Model\Entity\Video', $videosFromView);
    }

    /**
     * Tests for `add()` method
     * @test
     */
    public function testAdd()
    {
        $url = array_merge($this->url, ['action' => 'add']);

        $this->get($url);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate(ROOT . 'src/Template/Admin/Videos/add.ctp');

        $url = array_merge($url, ['?' => ['url' => 'invalidUrl']]);

        //GET request. Invalid url
        $this->get($url);
        $this->assertRedirect([]);
        $this->assertFlashMessage('This is not a YouTube video');

        $url = array_merge($url, ['?' => ['url' => 'https://www.youtube.com/watch?v=aaa']]);

        //GET request. Invalid Youtube ID
        $this->get($url);
        $this->assertRedirect([]);
        $this->assertFlashMessage('Unable to retrieve video informations. Probably the video is private');

        $url = array_merge($url, ['?' => ['url' => 'https://www.youtube.com/watch?v=6z4KK7RWjmk']]);

        //GET request. Now the url and Youtube ID are valid
        $this->get($url);
        $this->assertResponseOkAndNotEmpty();

        $videoFromView = $this->viewVariable('video');
        $this->assertNotEmpty($videoFromView);
        $this->assertInstanceof('MeCmsYoutube\Model\Entity\Video', $videoFromView);

        //POST request. Data are valid
        $this->post($url, $this->example);
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage('The operation has been performed correctly');

        //POST request. Data are invalid
        $this->post($url, ['title' => 'aa']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertResponseContains('The operation has not been performed correctly');

        $videoFromView = $this->viewVariable('video');
        $this->assertNotEmpty($videoFromView);
        $this->assertInstanceof('MeCmsYoutube\Model\Entity\Video', $videoFromView);
    }

    /**
     * Tests for `edit()` method
     * @test
     */
    public function testEdit()
    {
        $url = array_merge($this->url, ['action' => 'edit', 1]);

        $this->get($url);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate(ROOT . 'src/Template/Admin/Videos/edit.ctp');

        $videoFromView = $this->viewVariable('video');
        $this->assertNotEmpty($videoFromView);
        $this->assertInstanceof('MeCmsYoutube\Model\Entity\Video', $videoFromView);

        //Checks if the `created` field has been properly formatted
        $this->assertRegExp('/^\d{4}\-\d{2}\-\d{2}\s\d{2}\:\d{2}$/', $videoFromView->created);

        //POST request. Data are valid
        $this->post($url, ['title' => 'another title']);
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage('The operation has been performed correctly');

        //POST request. Data are invalid
        $this->post($url, ['title' => 'aa']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertResponseContains('The operation has not been performed correctly');

        $videoFromView = $this->viewVariable('video');
        $this->assertNotEmpty($videoFromView);
        $this->assertInstanceof('MeCmsYoutube\Model\Entity\Video', $videoFromView);
    }

    /**
     * Tests for `delete()` method
     * @test
     */
    public function testDelete()
    {
        $this->post(array_merge($this->url, ['action' => 'delete', 1]));
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage('The operation has been performed correctly');
    }

    /**
     * Tests that the admins and manangers can add and edit as another user
     * @test
     */
    public function testAdminsAndManagersCanAddAndEditAsAnotherUser()
    {
        foreach (['admin', 'manager'] as $userGroup) {
            $this->setUserGroup($userGroup);

            foreach ([1, 2] as $userId) {
                //Adds record
                $this->post(
                    array_merge($this->url, ['action' => 'add', '?' => ['url' => 'https://www.youtube.com/watch?v=6z4KK7RWjmk']]),
                    array_merge($this->example, ['user_id' => $userId])
                );
                $this->assertRedirect(['action' => 'index']);
                $this->assertFlashMessage('The operation has been performed correctly');

                $video = $this->Videos->find()->last();
                $this->assertEquals($userId, $video->user_id);

                //Edits record, adding +1 to the `user_id`
                $this->post(
                    array_merge($this->url, ['action' => 'edit', $video->id]),
                    array_merge($this->example, ['user_id' => $userId + 1])
                );
                $this->assertRedirect(['action' => 'index']);
                $this->assertFlashMessage('The operation has been performed correctly');

                $video = $this->Videos->findById($video->id)->first();
                $this->assertEquals($userId + 1, $video->user_id);

                $this->Videos->delete($video);
            }
        }
    }

    /**
     * Tests that the other users cannot add and edit as another user
     * @test
     */
    public function testOtherUsersCannotAddOrEditAsAnotherUser()
    {
        $this->setUserGroup('user');
        $this->setUserId(3);

        foreach ([1, 2] as $userId) {
            //Adds record
            $this->post(
                array_merge($this->url, ['action' => 'add', '?' => ['url' => 'https://www.youtube.com/watch?v=6z4KK7RWjmk']]),
                array_merge($this->example, ['user_id' => $userId])
            );
            $this->assertRedirect(['action' => 'index']);
            $this->assertFlashMessage('The operation has been performed correctly');

            $video = $this->Videos->find()->last();
            $this->assertEquals(3, $video->user_id);

            //Edits record, adding +1 to the `user_id`
            $this->post(
                array_merge($this->url, ['action' => 'edit', $video->id]),
                array_merge($this->example, ['user_id' => $userId + 1])
            );
            $this->assertRedirect(['action' => 'index']);
            $this->assertFlashMessage('The operation has been performed correctly');

            $video = $this->Videos->findById($video->id)->first();
            $this->assertEquals(3, $video->user_id);

            $this->Videos->delete($video);
        }
    }
}
