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
 * @see         MeCms\Utility\SitemapBuilder
 */
namespace MeCmsYoutube\Test\TestCase\Utility;

use Cake\TestSuite\TestCase;
use MeCmsYoutube\Utility\Youtube;

/**
 * YoutubeTest class
 */
class YoutubeTest extends TestCase
{
    /**
     * Test for `getInfo()` method
     * @test
     */
    public function testGetInfo()
    {
        $this->Youtube = $this->getMockBuilder(Youtube::class)
            ->setMethods(['_getInfoResponse'])
            ->getMock();

        $this->Youtube->expects($this->once())
            ->method('_getInfoResponse')
            ->will($this->returnCallback(function () {
                return file_get_contents(TEST_APP . 'examples' . DS . 'video.json');
            }));

        $result = $this->Youtube->getInfo('vlSR8Wlmpac');

        $this->assertEquals([
            'preview' => 'https://i.ytimg.com/vi/vlSR8Wlmpac/hqdefault.jpg',
            'text' => 'A video description',
            'title' => 'Beethoven - Symphony No. 9 in D minor: Ode to Joy [HD]',
            'seconds' => 778,
            'duration' => '12:58',
        ], (array)$result);
    }
}
