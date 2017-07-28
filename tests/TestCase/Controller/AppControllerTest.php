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

use Cake\Event\Event;
use MeCmsYoutube\Controller\AppController;
use MeTools\TestSuite\TestCase;

/**
 * AppControllerTest class
 */
class AppControllerTest extends TestCase
{
    /**
     * @var \MeCmsYoutube\Controller\AppController
     */
    protected $Controller;

    /**
     * @var \Cake\Event\Event
     */
    protected $Event;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Controller = new AppController;
    }

    /**
     * Tests for `beforeRender()` method
     * @test
     */
    public function testBeforeRender()
    {
        $this->Controller->beforeRender(new Event('myEvent'));
        $this->assertEquals('MeCmsYoutube.View/App', $this->Controller->viewBuilder()->getClassName());

        //Admin request
        $this->Controller = new AppController;
        $this->Controller->request = $this->Controller->request->withParam('prefix', ADMIN_PREFIX);

        $this->Controller->beforeRender(new Event('myEvent'));
        $this->assertEquals('MeCms.View/Admin', $this->Controller->viewBuilder()->getClassName());
    }
}
