<?php
/**
 * This file is part of MeYoutube.
 *
 * MeYoutube is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeYoutube is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeYoutube.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeYoutube\Shell;

use MeTools\Shell\Base\BaseShell;

/**
 * Applies updates
 */
class UpdateShell extends BaseShell {
	/**
	 * Rewrites the header for the shell
	 */
	protected function _welcome() { }
	
	/**
	 * Updates to 2.0.4-RC4 version
	 * @uses MeYoutube\Utility\Youtube::getInfo()
	 */
	public function to2v0v4vRC4() {
		$this->loadModel('MeYoutube.Videos');
		
		$videos = $this->Videos->find('all')->select(['id', 'youtube_id', 'duration', 'seconds']);
		
		foreach($videos as $video) {
			if(!empty($video->duration) && $video->duration != '00:00' && !empty($video->seconds))
				continue;
						
			$data = \MeYoutube\Utility\Youtube::getInfo($video->youtube_id);

			$video->duration = $data['duration'];
			$video->seconds = $data['seconds'];
			
			
			$this->out([$video->id, $data['duration'], $data['seconds']]);

			$this->Videos->query()->update()
				->set(['duration' => $data['duration'], 'seconds' => $data['seconds']])
				->where(['id' => $video->id])
				->execute();
		}
	}
}