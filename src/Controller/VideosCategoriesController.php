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
namespace MeYoutube\Controller;

use Cake\Cache\Cache;
use Cake\Datasource\Exception\RecordNotFoundException;
use MeYoutube\Controller\AppController;

/**
 * VideosCategories controller
 * @property \MeYoutube\Model\Table\VideosCategoriesTable $VideosCategories
 */
class VideosCategoriesController extends AppController {
	/**
     * Lists videos categories
     */
    public function index() {
		$categories = $this->VideosCategories->find('active')
			->select(['title', 'slug'])
			->order(['title' => 'ASC'])
			->cache('categories_index', $this->VideosCategories->cache)
			->all();
        
        $this->set(compact('categories'));
    }
	
	/**
	 * Lists videos for a category
	 * @param string $category Category slug
	 */
	public function view($category = NULL) {
		//The category can be passed as query string, from a widget
		if($this->request->query('q')) {
			return $this->redirect([$this->request->query('q')]);
        }
        
        $page = $this->request->query('page') ? $this->request->query('page') : 1;
        
		//Sets the cache name
		$cache = sprintf('index_category_%s_limit_%s_page_%s', md5($category), $this->paginate['limit'], $page);
		
		//Tries to get data from the cache
		list($videos, $paging) = array_values(Cache::readMany([$cache, sprintf('%s_paging', $cache)], $this->VideosCategories->cache));
		
		//If the data are not available from the cache
		if(empty($videos) || empty($paging)) {
			$query = $this->VideosCategories->Videos->find('active')
				->contain([
                    'Categories' => function($q) {
                        return $q->select(['title', 'slug']);
                    },
                    'Users' => function($q) {
                        return $q->select(['first_name', 'last_name']);
                    },
				])
				->select(['id', 'youtube_id', 'title', 'subtitle', 'text', 'created'])
				->where([
                    'Categories.slug' => $category,
                    'is_spot' => FALSE,
                ])
				->order([sprintf('%s.created', $this->VideosCategories->Videos->alias()) => 'DESC']);
					
			if($query->isEmpty()) {
				throw new RecordNotFoundException(__d('me_cms', 'Record not found'));
            }
            
			$videos = $this->paginate($query)->toArray();
						
			//Writes on cache
			Cache::writeMany([
                $cache => $videos,
                sprintf('%s_paging', $cache) => $this->request->param('paging'),
            ], $this->VideosCategories->cache);
		}
		//Else, sets the paging parameter
		else {
			$this->request->params['paging'] = $paging;
        }
        
		$this->set(am([
            'category' => $videos[0]->category->title,
        ], compact('videos')));
	}
}