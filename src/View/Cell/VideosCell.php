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
namespace MeYoutube\View\Cell;

use MeTools\Cache\Cache;
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
	 * @uses MeTools\Network\Request::isHere()
	 * @uses MeYoutube\Model\Table\VideosTable::checkIfCacheIsValid()
	 */
	public function categories() {
		//Returns on categories index
		if($this->request->isHere(['_name' => 'videos_categories']))
			return;
		
		//Checks if the cache is valid
		$this->Videos->checkIfCacheIsValid();
		
		//Tries to get data from the cache
		$categories = Cache::read($cache = 'widget_categories', $this->Videos->cache);
		
		//If the data are not available from the cache
        if(empty($categories)) {
			$categories = $this->Videos->Categories->find('active')
				->select(['title', 'slug', 'video_count'])
				->order(['title' => 'ASC'])
				->toArray();
			
			foreach($categories as $k => $category) {
				$categories[$category->slug] = sprintf('%s (%d)', $category->title, $category->video_count);
				unset($categories[$k]);
			}
			
            Cache::write($cache, $categories, $this->Videos->cache);
		}
		
		$this->set(compact('categories'));
	}
	
	/**
	 * Latest widget
	 * @param int $limit Limit
	 * @uses MeTools\Network\Request::isAction()
	 * @uses MeYoutube\Model\Table\VideosTable::checkIfCacheIsValid()
	 */
    public function latest($limit = 1) {
		//Returns on index, except for category
		if($this->request->isAction('index', 'Videos') && !$this->request->param('slug'))
			return;
		
		//Checks if the cache is valid
		$this->Videos->checkIfCacheIsValid();

		$this->set('videos', $this->Videos->find('active')
			->select(['id', 'youtube_id', 'title', 'description'])
			->limit($limit)
			->order(['created' => 'DESC'])
			->cache(sprintf('widget_latest_%d', $limit), $this->Videos->cache)
			->toArray()
		);
    }
	
	/**
	 * Random widget
	 * @param int $limit Limit
	 * @uses MeTools\Network\Request::isController()
	 * @uses MeYoutube\Model\Table\VideosTable::checkIfCacheIsValid()
	 */
	public function random($limit = 1) {
		//Returns on the same controllers
		if($this->request->isController(['Videos', 'VideosCategories']))
			return;
		
		//Checks if the cache is valid
		$this->Videos->checkIfCacheIsValid();
		
		//Returns, if there are no records available
		if(Cache::read($cache = 'no_videos', $this->Videos->cache))
			return;

		//Gets videos
		$videos = $this->Videos->find('active')
			->select(['id', 'youtube_id', 'title', 'description'])
			->limit($limit)
			->order('rand()')
			->toArray();
		
		//Writes on cache, if there are no records available
		if(empty($videos))
			Cache::write($cache, TRUE, $this->Videos->cache);
		
		$this->set(compact('videos'));
	}
	
	/**
	 * Search widget
	 */
	public function search() {
		//For this widget, control of the action takes place in the view
	}
}