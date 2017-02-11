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

use Cake\TestSuite\TestCase;
use MeCmsYoutube\Model\Entity\Video;

/**
 * VideoTest class
 */
class VideoTest extends TestCase
{
    /**
     * Test for `__construct()` method
     * @test
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('MeCmsYoutube\Model\Entity\Video', new Video);
    }

    /**
     * Test for fields that cannot be mass assigned using newEntity() or
     *  patchEntity()
     * @test
     */
    public function testNoAccessibleProperties()
    {
        $entity = new Video;

        $this->assertFalse($entity->accessible('id'));
        $this->assertFalse($entity->accessible('modified'));
    }

    /**
     * Test for `_getPreview()` method
     * @test
     */
    public function testPreviewGetMutator()
    {
        $entity = new Video;

        $this->assertNull($entity->preview);

        $entity->youtube_id = 't3217H8JppI';

        $this->assertEquals('http://img.youtube.com/vi/t3217H8JppI/0.jpg', $entity->preview);
    }

    /**
     * Test for `_getYoutubeUrl()` method
     * @test
     */
    public function testYoutubeUrlGetMutator()
    {
        $entity = new Video;

        $this->assertNull($entity->youtube_url);

        $entity->youtube_id = 't3217H8JppI';

        $this->assertEquals('http://youtu.be/t3217H8JppI', $entity->youtube_url);
    }
}
