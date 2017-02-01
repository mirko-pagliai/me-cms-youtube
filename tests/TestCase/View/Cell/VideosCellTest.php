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
namespace MeCmsYoutube\Test\TestCase\View\Cell;

use Cake\Cache\Cache;
use Cake\Network\Request;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use MeCms\View\Helper\WidgetHelper;
use MeCmsYoutube\View\View\AppView as View;

/**
 * VideosCellTest class
 */
class VideosCellTest extends TestCase
{
    /**
     * @var \MeCmsYoutube\Model\Table\VideosTable
     */
    protected $Videos;

    /**
     * @var \MeCmsYoutube\View\View\AppView
     */
    protected $View;

    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
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
        Cache::disable();

        $this->Videos = TableRegistry::get('MeCmsYoutube.Videos');

        $this->View = new View;
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->Videos, $this->View);
    }

    /**
     * Test for `categories()` method
     * @test
     */
    public function testCategories()
    {
        $widget = ME_CMS_YOUTUBE . '.Videos::categories';

        $result = $this->View->cell($widget)->render();

        $expected = [
            ['div' => ['class' => 'widget']],
            'h4' => ['class' => 'widget-title'],
            'Videos categories',
            '/h4',
            ['div' => ['class' => 'widget-content']],
            'form' => ['method' => 'get', 'accept-charset' => 'utf-8', 'action' => '/videos/category/category'],
            ['div' => ['class' => 'form-group input select']],
            'select' => ['name' => 'q', 'onchange' => 'send_form(this)', 'class' => 'form-control'],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => 'first-video-category']],
            'First video category (1)',
            '/option',
            ['option' => ['value' => 'sub-sub-video-category']],
            'Sub sub video category (2)',
            '/option',
            '/select',
            '/div',
            '/form',
            '/div',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        //Renders as list
        $result = $this->View->cell($widget, ['render' => 'list'])->render();

        $expected = [
            ['div' => ['class' => 'widget']],
            'h4' => ['class' => 'widget-title'],
            'Videos categories',
            '/h4',
            ['div' => ['class' => 'widget-content']],
            'ul' => ['class' => 'fa-ul'],
            ['li' => true],
            ['i' => ['class' => 'fa fa-caret-right fa-li']],
            ' ',
            '/i',
            ['a' => ['href' => '/videos/category/first-video-category', 'title' => 'First video category']],
            'First video category',
            '/a',
            '/li',
            ['li' => true],
            ['i' => ['class' => 'fa fa-caret-right fa-li']],
            ' ',
            '/i',
            ['a' => ['href' => '/videos/category/sub-sub-video-category', 'title' => 'Sub sub video category']],
            'Sub sub video category',
            '/a',
            '/li',
            '/ul',
            '/div',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        //Empty on categories index
        $request = new Request(Router::url(['_name' => 'videosCategories']));
        $this->View = new View($request);
        $result = $this->View->cell($widget)->render();
        $this->assertEmpty($result);
    }

    /**
     * Test for `latest()` method
     * @test
     */
    public function testLatest()
    {
        $widget = ME_CMS_YOUTUBE . '.Videos::latest';

        //Tries with a limit of 1
        $result = $this->View->cell($widget, ['limit' => 1])->render();

        $expected = [
            ['div' => ['class' => 'widget']],
            'h4' => ['class' => 'widget-title'],
            'Latest video',
            '/h4',
            ['div' => ['class' => 'widget-content']],
            ['div' => ['class' => 'content-preview']],
            'a' => ['href' => '/video/2'],
            ['div' => true],
            ['div' => true],
            ['div' => ['class' => 'content-title']],
            'Second video title',
            '/div',
            ['div' => ['class' => 'content-text']],
            'Second video text',
            '/div',
            '/div',
            '/div',
            'img' => ['src', 'alt' => 'Second video title', 'class' => 'img-responsive'],
            '/a',
            '/div',
            '/div',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        //Tries with a limit of 2
        $result = $this->View->cell($widget, ['limit' => 2])->render();

        $expected = [
            ['div' => ['class' => 'widget']],
            'h4' => ['class' => 'widget-title'],
            'Latest 2 videos',
            '/h4',
            ['div' => ['class' => 'widget-content']],
            ['div' => ['class' => 'content-preview']],
            ['a' => ['href' => '/video/2']],
            ['div' => true],
            ['div' => true],
            ['div' => ['class' => 'content-title']],
            'Second video title',
            '/div',
            ['div' => ['class' => 'content-text']],
            'Second video text',
            '/div',
            '/div',
            '/div',
            ['img' => ['src', 'alt' => 'Second video title', 'class' => 'img-responsive']],
            '/a',
            '/div',
            ['div' => ['class' => 'content-preview']],
            ['a' => ['href' => '/video/1']],
            ['div' => true],
            ['div' => true],
            ['div' => ['class' => 'content-title']],
            'First video title',
            '/div',
            ['div' => ['class' => 'content-text']],
            'First video text',
            '/div',
            '/div',
            '/div',
            ['img' => ['src', 'alt' => 'First video title', 'class' => 'img-responsive']],
            '/a',
            '/div',
            '/div',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        //Empty on videos index
        $request = new Request(Router::url(['_name' => 'videos']));
        $this->View = new View($request);
        $result = $this->View->cell($widget)->render();
        $this->assertEmpty($result);
    }

    /**
     * Test for `months()` method
     * @test
     */
    public function testMonths()
    {
        $widget = ME_CMS_YOUTUBE . '.Videos::months';

        $result = $this->View->cell($widget)->render();

        $expected = [
            ['div' => ['class' => 'widget']],
            'h4' => ['class' => 'widget-title'],
            'Videos by month',
            '/h4',
            ['div' => ['class' => 'widget-content']],
            'form' => ['method' => 'get', 'accept-charset' => 'utf-8', 'action' => '/videos/' . date('Y/m')],
            ['div' => ['class' => 'form-group input select']],
            'select' => ['name' => 'q', 'onchange' => 'send_form(this)', 'class' => 'form-control'],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => '2016/12']],
            'December 2016 (1)',
            '/option',
            ['option' => ['value' => '2016/11']],
            'November 2016 (1)',
            '/option',
            '/select',
            '/div',
            '/form',
            '/div',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        //Renders as list
        $result = $this->View->cell($widget, ['render' => 'list'])->render();

        $expected = [
            ['div' => ['class' => 'widget']],
            'h4' => ['class' => 'widget-title'],
            'Videos by month',
            '/h4',
            ['div' => ['class' => 'widget-content']],
            'ul' => ['class' => 'fa-ul'],
            ['li' => true],
            ['i' => ['class' => 'fa fa-caret-right fa-li']],
            ' ',
            '/i',
            ['a' => ['href' => '/videos/2016/12', 'title' => 'December 2016']],
            'December 2016',
            '/a',
            '/li',
            ['li' => true],
            ['i' => ['class' => 'fa fa-caret-right fa-li']],
            ' ',
            '/i',
            ['a' => ['href' => '/videos/2016/11', 'title' => 'November 2016']],
            'November 2016',
            '/a',
            '/li',
            '/ul',
            '/div',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        //Empty on videos index
        $request = new Request(Router::url(['_name' => 'videos']));
        $this->View = new View($request);
        $result = $this->View->cell($widget)->render();
        $this->assertEmpty($result);
    }

    /**
     * Test for `search()` method
     * @test
     */
    public function testSearch()
    {
        $widget = ME_CMS_YOUTUBE . '.Videos::search';

        $result = $this->View->cell($widget)->render();

        $expected = [
            ['div' => ['class' => 'widget']],
            'h4' => ['class' => 'widget-title'],
            'Search videos',
            '/h4',
            ['div' => ['class' => 'widget-content']],
            'form' => ['method' => 'get', 'accept-charset' => 'utf-8', 'class' => 'form-inline', 'action' => '/videos/search'],
            ['div' => ['class' => 'input form-group text']],
            ['div' => ['class' => 'input-group']],
            'input' => ['type' => 'text', 'name' => 'p', 'placeholder' => 'Search...', 'class' => 'form-control'],
            'span' => ['class' => 'input-group-btn'],
            'button' => ['class' => 'btn-primary btn', 'type' => 'button'],
            'i' => ['class' => 'fa fa-search'],
            ' ',
            '/i',
            '/button',
            '/span',
            '/div',
            '/div',
            '/form',
            '/div',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        //Empty on search
        $request = new Request(Router::url(['_name' => 'videosSearch']));
        $this->View = new View($request);
        $result = $this->View->cell($widget)->render();
        $this->assertEmpty($result);
    }
}
