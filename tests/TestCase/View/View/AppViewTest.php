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
namespace MeCmsYoutube\Test\TestCase\View\View;

use Cake\Core\Configure;
use Cake\Network\Request;
use MeCmsYoutube\View\View\AppView as View;
use MeTools\TestSuite\TestCase;

/**
 * AppViewTest class
 */
class AppViewTest extends TestCase
{
    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        //Disable widgets
        Configure::write('Widgets.general', []);

        $this->View = new View(new Request);
    }

    /**
     * Tests for `_setBlocks()` method
     * @test
     */
    public function testSetBlocks()
    {
        $result = $this->View->render(false);

        $this->assertRegExp('/' . preg_quote('<link href="/videos/rss" type="application/rss+xml" rel="alternate" title="Latest videos"/>', '/') . '/', $result);
    }

    /**
     * Tests for `renderLayout()` method
     * @test
     */
    public function testRenderLayout()
    {
        $result = $this->View->render(false);
        $this->assertNotEmpty($result);
        $this->assertEquals('default', $this->View->getLayout());
        $this->assertEquals(null, $this->View->getTheme());
        $this->assertEquals(ME_CMS, $this->View->plugin);
    }
}
