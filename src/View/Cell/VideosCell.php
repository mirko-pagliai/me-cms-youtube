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
namespace MeYoutube\View\Cell;

use Cake\Cache\Cache;
use Cake\View\Cell;

/**
 * Videos cell
 */
class VideosCell extends Cell {
	/**
	 * Constructor. It loads the model
	 * @param \MeTools\Network\Request $request The request to use in the cell
	 * @param \Cake\Network\Response $response The request to use in the cell
	 * @param \Cake\Event\EventManager $eventManager The eventManager to bind events to
	 * @param array $cellOptions Cell options to apply
	 * @uses Cake\View\Cell::__construct()
	 */
	public function __construct(\MeTools\Network\Request $request = NULL, \Cake\Network\Response $response = NULL, \Cake\Event\EventManager $eventManager = NULL, array $cellOptions = []) {
		parent::__construct($request, $response, $eventManager, $cellOptions);
		
		//Loads the Videos model
		$this->loadModel('MeYoutube.Videos');
	}
	
	/**
	 * Categories widget
	 * @uses MeTools\Network\Request::isCurrent()
	 */
	public function categories() {
		//Returns on categories index
		if($this->request->isCurrent(['_name' => 'videos_categories']))
			return;
		
		//Tries to get data from the cache
		$categories = Cache::read($cache = 'widget_categories', 'videos');
		
		//If the data are not available from the cache
        if(empty($categories)) {
			foreach($this->Videos->Categories->find('active')
					->select(['title', 'slug', 'video_count'])
					->order(['title' => 'ASC'])
					->toArray() as $k => $category)
				$categories[$category->slug] = sprintf('%s (%d)', $category->title, $category->video_count);
			
            Cache::write($cache, $categories, 'videos');
		}
		
		$this->set(compact('categories'));
	}
	
	/**
	 * Latest widget
	 * @param string $limit Limit
	 * @uses MeTools\Network\Request::isAction()
	 */
    public function latest($limit = NULL) {
		//Returns on index, except for category
		if($this->request->isAction('index', 'Videos') && !$this->request->param('slug'))
			return;

		$this->set('videos', $this->Videos->find('active')
			->select(['id', 'youtube_id', 'title', 'description'])
			->limit($limit = empty($limit) ? 1 : $limit)
			->order(['created' => 'DESC'])
			->cache(sprintf('widget_latest_%d', $limit), 'videos')
			->toArray()
		);
    }
	
	/**
	 * Search widget
	 */
	public function search() { }
}