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
 * @copyright	Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeYoutube\Shell;

use MeCms\Shell\BaseUpdateShell;

/**
 * Applies updates
 */
class UpdateShell extends BaseUpdateShell {
	/**
	 * Updates to 2.4.0 version
	 * @uses MeCms\Shell\BaseUpdateShell::$connection
	 */
	public function to2v4v0() {
		$this->loadModel('MeYoutube.Videos');
        
        //Renames the "description" field as "text"
        if($this->_checkColumn('description', $this->Videos->table())) {
            $this->connection->execute(sprintf('ALTER TABLE `%s` CHANGE `description` `text` TEXT NULL DEFAULT NULL;', $this->Videos->table()));
        }
    }
    
	/**
	 * Updates to 2.3.0 version
	 * @uses MeCms\Shell\BaseUpdateShell::$connection
     * @uses MeCms\Shell\BaseUpdateShell::_checkColumn()
	 */
	public function to2v3v0() {
		$this->loadModel('MeYoutube.VideosCategories');
        
        //Adds "created" field to the videos categories table and sets the default value
        if(!$this->_checkColumn('created', $this->VideosCategories->table())) {
            $this->connection->execute(sprintf('ALTER TABLE `%s` ADD `created` DATETIME NULL AFTER `video_count`;', $this->VideosCategories->table()));
            $this->VideosCategories->query()->update()->set(['created' => $this->now])->execute();
        }
        
        //Adds "modified" field to the videos categories table and sets the default value
        if(!$this->_checkColumn('modified', $this->VideosCategories->table())) {
            $this->connection->execute(sprintf('ALTER TABLE `%s` ADD `modified` DATETIME NULL AFTER `created`;', $this->VideosCategories->table()));
            $this->VideosCategories->query()->update()->set(['modified' => $this->now])->execute();
        }
    }
    
	/**
	 * Updates to 2.0.4-RC4 version
	 * @uses MeYoutube\Utility\Youtube::getInfo()
	 */
	public function to2v0v4vRC4() {
		$this->loadModel('MeYoutube.Videos');
		
		$videos = $this->Videos->find('all')
			->select(['id', 'youtube_id', 'duration', 'seconds'])
			->where(['OR' => [
				'duration' => '00:00',
				'duration' => '',
				'seconds' => 0
			]]);
		
		foreach($videos as $video) {
			$data = \MeYoutube\Utility\Youtube::getInfo($video->youtube_id);

			$video->duration = $data['duration'];
			$video->seconds = $data['seconds'];

			$this->Videos->query()->update()
				->set(['duration' => $data['duration'], 'seconds' => $data['seconds']])
				->where(['id' => $video->id])
				->execute();
		}
	}
	
	/**
	 * Gets the option parser instance and configures it.
	 * @return ConsoleOptionParser
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		
		return $parser->addSubcommands([
            'to2v4v0' => ['help' => __d('me_cms', 'Updates to {0} version', '2.4.0')],
            'to2v3v0' => ['help' => __d('me_cms', 'Updates to {0} version', '2.3.0')],
			'to2v0v4vRC4' => ['help' => __d('me_cms', 'Updates to {0} version', '2.0.4-RC4')],
		]);
	}
}