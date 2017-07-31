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
namespace MeCmsYoutube\Test\TestCase\Utility;

use Cake\View\View;
use MeCmsYoutube\View\Helper\MenuHelper;
use MeTools\TestSuite\TestCase;

/**
 * YoutubeTest class
 */
class MenuHelperTest extends TestCase
{
    /**
     * @var \MeCmsYoutube\View\Helper\MenuHelper
     */
    protected $Menu;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Menu = new MenuHelper(new View);
    }

    /**
     * Tests for `videos()` method
     * @test
     */
    public function testVideos()
    {
        list($menu, $title, $options) = $this->Menu->videos();

        $this->assertEquals([
            '<a href="/me-cms-youtube/admin/videos" title="List videos">List videos</a>',
            '<a href="/me-cms-youtube/admin/videos/add" title="Add video">Add video</a>',
        ], $menu);
        $this->assertEquals('Videos', $title);
        $this->assertEquals(['icon' => 'film'], $options);

        $expected = [
            '<a href="/me-cms-youtube/admin/videos" title="List videos">List videos</a>',
            '<a href="/me-cms-youtube/admin/videos/add" title="Add video">Add video</a>',
            '<a href="/me-cms-youtube/admin/videos-categories" title="List categories">List categories</a>',
            '<a href="/me-cms-youtube/admin/videos-categories/add" title="Add category">Add category</a>',
        ];

        //Menu for manager users
        $this->Menu->Auth->initialize(['group' => ['name' => 'manager']]);
        list($menu) = $this->Menu->videos();

        $this->assertEquals($expected, $menu);

        //Menu for admin users
        $this->Menu->Auth->initialize(['group' => ['name' => 'admin']]);
        list($menu) = $this->Menu->videos();

        $this->assertEquals($expected, $menu);
    }
}
