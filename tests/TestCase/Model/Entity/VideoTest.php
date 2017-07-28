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
namespace MeCmsYoutube\Test\TestCase\Model\Entity;

use MeCmsYoutube\Model\Entity\Video;
use MeCmsYoutube\Utility\Youtube;
use MeTools\TestSuite\TestCase;

/**
 * VideoTest class
 */
class VideoTest extends TestCase
{
    /**
     * @var \MeCms\Model\Entity\Video
     */
    protected $Video;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Video = new Video;
    }

    /**
     * Test for `__construct()` method
     * @test
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('MeCmsYoutube\Model\Entity\Video', $this->Video);
    }

    /**
     * Test for fields that cannot be mass assigned using newEntity() or
     *  patchEntity()
     * @test
     */
    public function testNoAccessibleProperties()
    {
        $this->assertFalse($this->Video->isAccessible('id'));
        $this->assertFalse($this->Video->isAccessible('modified'));
    }

    /**
     * Test for virtual fields
     * @test
     */
    public function testVirtualFields()
    {
        $this->assertEquals(['preview', 'youtube_url'], $this->Video->getVirtual());
    }

    /**
     * Test for `_getPreview()` method
     * @test
     */
    public function testPreviewGetMutator()
    {
        $this->assertNull($this->Video->preview);

        $this->Video->youtube_id = 't3217H8JppI';
        $this->assertEquals([
            'preview' => Youtube::getPreview('t3217H8JppI'),
            'width' => 480,
            'height' => 360,
        ], $this->Video->preview);
    }

    /**
     * Test for `_getYoutubeUrl()` method
     * @test
     */
    public function testYoutubeUrlGetMutator()
    {
        $this->assertNull($this->Video->youtube_url);

        $this->Video->youtube_id = 't3217H8JppI';
        $this->assertEquals('http://youtu.be/t3217H8JppI', $this->Video->youtube_url);
    }
}
