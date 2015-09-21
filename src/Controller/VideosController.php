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
namespace MeYoutube\Controller;

use Cake\Cache\Cache;
use MeCms\Controller\AppController;

/**
 * Videos controller
 * @property \MeYoutube\Model\Table\VideosTable $Videos
 */
class VideosController extends AppController {
	/**
     * Lists videos
	 * @param string $category Category slug (optional)
	 * @uses MeYoutube\Model\Table\VideosTable::checkIfCacheIsValid()
	 */
    public function index($category = NULL) {
		//The category can be passed as query string, from a widget
		if($this->request->query('q'))
			$this->redirect([$this->request->query('q')]);
		
		//Checks if the cache is valid
		$this->Videos->checkIfCacheIsValid();
		
		//Sets the initial cache name
		$cache = 'index';
		
		//Sets the initial conditions
		$conditions = ['is_spot' => FALSE];
		
		//Checks if has been specified a category
		if(!empty($category)) {
			//Adds the category to the conditions, if it has been specified
			$conditions['Categories.slug'] = $category;
			
			//Updates the cache name, adding the category name
			$cache = sprintf('%s_%s', $cache, md5($category));
		}
		
		//Updates the cache name with the query limit and the number of the page
		$cache = sprintf('%s_limit_%s', $cache, $this->paginate['limit']);
		$cache = sprintf('%s_page_%s', $cache, $this->request->query('page') ? $this->request->query('page') : 1);
		
		//Tries to get data from the cache
		list($videos, $paging) = array_values(Cache::readMany([$cache, sprintf('%s_paging', $cache)], 'videos'));
		
		//If the data are not available from the cache
		if(empty($videos) || empty($paging)) {
			$videos = $this->paginate(
				$this->Videos->find('active')
					->contain([
						'Categories'	=> ['fields' => ['title', 'slug']],
						'Users'			=> ['fields' => ['first_name', 'last_name']]
					])
					->select(['id', 'youtube_id', 'title', 'subtitle', 'description', 'created'])
					->where($conditions)
					->order([sprintf('%s.created', $this->name) => 'DESC'])
			)->toArray();
						
			//Writes on cache
			Cache::writeMany([$cache => $videos, sprintf('%s_paging', $cache) => $this->request->param('paging')], 'videos');
		}
		//Else, sets the paging parameter
		else
			$this->request->params['paging'] = $paging;
				
		//Sets the category title as title, if has been specified a category
		if(!empty($category) && !empty($videos[0]->category->title))
			$this->set('title', $videos[0]->category->title);
		
        $this->set(compact('videos'));
    }
	
    /**
     * Views video
     * @param string $id Video ID
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function view($id = NULL) {
		$video = $this->Videos->find('active')
			->contain([
				'Categories'	=> ['fields' => ['title', 'slug']],
				'Users'			=> ['fields' => ['first_name', 'last_name']]
			])
			->select(['id', 'youtube_id', 'title', 'subtitle', 'description', 'created'])
			->where([sprintf('%s.id', $this->name) => $id])
			->cache(sprintf('view_%s', md5($id)), 'videos')
			->first();
		
		$this->set(am(['image_src' => $video->preview], compact('video')));
    }
}