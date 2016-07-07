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
use Cake\I18n\Time;
use MeYoutube\Controller\AppController;

/**
 * Videos controller
 * @property \MeYoutube\Model\Table\VideosTable $Videos
 */
class VideosController extends AppController {
	/**
	 * Called before the controller action. 
	 * You can use this method to perform logic that needs to happen before each controller action.
	 * @param \Cake\Event\Event $event An Event instance
	 * @see http://api.cakephp.org/3.2/class-Cake.Controller.Controller.html#_beforeFilter
	 * @uses MeCms\Controller\AppController::beforeFilter()
	 */
	public function beforeFilter(\Cake\Event\Event $event) {
        parent::beforeFilter($event);
        
        $this->Auth->deny('preview');
    }
    
	/**
     * Lists videos
	 */
    public function index() {
        $page = $this->request->query('page') ? $this->request->query('page') : 1;
        
		//Sets the cache name
		$cache = sprintf('index_limit_%s_page_%s', $this->paginate['limit'], $page);
		
		//Tries to get data from the cache
		list($videos, $paging) = array_values(Cache::readMany([$cache, sprintf('%s_paging', $cache)], $this->Videos->cache));
		
		//If the data are not available from the cache
		if(empty($videos) || empty($paging)) {
            $query = $this->Videos->find('active')
                ->select(['id', 'youtube_id', 'title', 'subtitle', 'text', 'created'])
                ->contain([
                    'Categories' => function($q) {
                        return $q->select(['title', 'slug']);
                    },
                    'Users' => function($q) {
                        return $q->select(['first_name', 'last_name']);
                    },
                ])
                ->where(['is_spot' => FALSE])
                ->order([sprintf('%s.created', $this->Videos->alias()) => 'DESC']);
            
			$videos = $this->paginate($query)->toArray();
						
			//Writes on cache
			Cache::writeMany([
                $cache => $videos,
                sprintf('%s_paging', $cache) => $this->request->param('paging'),
            ], $this->Videos->cache);
		}
		//Else, sets the paging parameter
		else {
			$this->request->params['paging'] = $paging;
        }
		
        $this->set(compact('videos'));
    }
    
    /**
     * List videos for a specific date:
     * 
     * The date must be passed in the format:
     * <pre>YYYY/MM/dd</pre>
     * The month and day are optional.  
     * You can also use the special keywords "today" and "yesterday".
     * 
     * Examples:
     * <pre>/index_by_date/2016/06/11</pre>
     * <pre>/index_by_date/2016/06</pre>
     * <pre>/index_by_date/2016</pre>
     * <pre>/index_by_date/today</pre>
     * <pre>/index_by_date/yesterday</pre>
     * @param string $date
     */
    public function index_by_date($date = NULL) {
        //Data can be passed as query string, from a widget
		if($this->request->query('q')) {
            return $this->redirect([$this->request->query('q')]);
        }
        
        //Sets `$year`, `$month` and `$day`
        //`$month` and `$day` may be `NULL`
        if($date === 'today' || $date === 'yesterday') {
            $date = new Time($date === 'today' ? 'now' : '1 days ago');
            
            list($year, $month, $day) = explode('/', $date->i18nFormat('YYYY/MM/dd'));
        }
        else {
            list($year, $month, $day) = am(explode('/', $date), [NULL, NULL, NULL]);
        }
        
        //Sets the start date
        $start = (new Time())
            ->setDate($year, empty($month) ? 1 : $month, empty($day) ? 1 : $day)
            ->setTime(0, 0, 0);
        
        //Sets the end date
        if($year && $month && $day) {
            $end = (new Time($start))->addDay(1);
        }
        elseif($year && $month) {
            $end = (new Time($start))->addMonth(1);
        }
        else {
            $end = (new Time($start))->addYear(1);
        }
        
        $page = $this->request->query('page') ? $this->request->query('page') : 1;
        
		//Sets the cache name
		$cache = sprintf('index_date_%s_limit_%s_page_%s', md5(serialize([$start, $end])), $this->paginate['limit'], $page);
		
		//Tries to get data from the cache
		list($videos, $paging) = array_values(Cache::readMany([$cache, sprintf('%s_paging', $cache)], $this->Videos->cache));
		
		//If the data are not available from the cache
		if(empty($videos) || empty($paging)) {            
            $query = $this->Videos->find('active')
                ->select(['id', 'youtube_id', 'title', 'subtitle', 'text', 'created'])
                ->contain([
                    'Categories' => function($q) {
                        return $q->select(['title', 'slug']);
                    },
                    'Users' => function($q) {
                        return $q->select(['first_name', 'last_name']);
                    },
                ])
                ->order([sprintf('%s.created', $this->Videos->alias()) => 'DESC'])
                ->where([
                    'is_spot' => FALSE,
                    sprintf('%s.created >=', $this->Videos->alias()) => $start,
                    sprintf('%s.created <', $this->Videos->alias()) => $end,
                ])
                ->order([sprintf('%s.created', $this->Videos->alias()) => 'DESC']);
			
			$videos = $this->paginate($query)->toArray();
						
			//Writes on cache
			Cache::writeMany([
                $cache => $videos,
                sprintf('%s_paging', $cache) => $this->request->param('paging'),
            ], $this->Videos->cache);
		}
		//Else, sets the paging parameter
		else {
			$this->request->params['paging'] = $paging;
        }
        
        $this->set(compact('videos', 'year', 'month', 'day'));
    }
	
	/**
	 * This allows backward compatibility for URLs like:
	 * <pre>/videos/page:3</pre>
	 * <pre>/videos/page:3/sort:Video.created/direction:desc</pre>
	 * These URLs will become:
	 * <pre>/videos?page=3</pre>
	 * @param int $page Page number
	 */
	public function index_compatibility($page) {
		return $this->redirect(['_name' => 'videos', '?' => ['page' => $page]], 301);
	}
	
	/**
	 * Lists videos as RSS
	 * @throws \Cake\Network\Exception\ForbiddenException
	 */
	public function rss() {
		//This method works only for RSS
		if(!$this->RequestHandler->isRss()) {
            throw new \Cake\Network\Exception\ForbiddenException();
        }
        
		$videos = $this->Videos->find('active')
			->select(['id', 'youtube_id', 'title', 'text', 'created'])
			->where(['is_spot' => FALSE])
			->limit(config('default.records_for_rss'))
			->order([sprintf('%s.created', $this->Videos->alias()) => 'DESC'])
			->cache('rss', $this->Videos->cache);
        
        $this->set(compact('videos'));
	}
	
	/**
	 * Searches videos
	 * @uses MeCms\Controller\AppController::_checkLastSearch()
	 */
	public function search() {
        $pattern = $this->request->query('p');
        
		if($pattern) {
			//Checks if the pattern is at least 4 characters long
			if(strlen($pattern) >= 4) {
				if($this->_checkLastSearch($pattern)) {
					$this->paginate['limit'] = config('default.records_for_searches');
					
					//Sets the initial cache name
					$cache = sprintf('search_%s', md5($pattern));

					//Updates the cache name with the query limit and the number of the page
					$cache = sprintf('%s_limit_%s', $cache, $this->paginate['limit']);
					$cache = sprintf('%s_page_%s', $cache, $this->request->query('page') ? $this->request->query('page') : 1);

					//Tries to get data from the cache
					list($videos, $paging) = array_values(Cache::readMany([$cache, sprintf('%s_paging', $cache)], $this->Videos->cache));

					//If the data are not available from the cache
					if(empty($videos) || empty($paging)) {
                        $query = $this->Videos->find('active')
                            ->select(['id', 'title', 'text', 'created'])
                            ->where(['OR' => [
                                'title LIKE' => sprintf('%%%s%%', $pattern),
                                'subtitle LIKE' => sprintf('%%%s%%', $pattern),
                                'text LIKE' => sprintf('%%%s%%', $pattern),
                            ]])
                            ->order([sprintf('%s.created', $this->Videos->alias()) => 'DESC']);
                        
						$videos = $this->paginate($query)->toArray();

						//Writes on cache
						Cache::writeMany([$cache => $videos, sprintf('%s_paging', $cache) => $this->request->param('paging')], $this->Videos->cache);
					}
					//Else, sets the paging parameter
					else {
						$this->request->params['paging'] = $paging;
                    }
                    
					$this->set(compact('videos'));
				}
				else {
					$this->Flash->alert(__d('me_cms', 'You have to wait {0} seconds to perform a new search', config('security.search_interval')));
                }
			}
			else {
				$this->Flash->alert(__d('me_cms', 'You have to search at least a word of {0} characters', 4));
            }
		}
        
        $this->set(compact('pattern'));
	}
	
    /**
     * Views video
     * @param string $id Video ID
	 * @uses MeYoutube\Model\Table\VideosTable::getRandomSpots()
     */
    public function view($id = NULL) {		
		$video = $this->Videos->find('active')
			->select(['id', 'youtube_id', 'title', 'subtitle', 'text', 'active', 'is_spot', 'created', 'modified'])
			->contain([
                'Categories' => function($q) {
                    return $q->select(['title', 'slug']);
                },
                'Users' => function($q) {
                    return $q->select(['first_name', 'last_name']);
                },
			])
			->where([sprintf('%s.id', $this->Videos->alias()) => $id])
			->cache(sprintf('view_%s', md5($id)), $this->Videos->cache)
			->firstOrFail();
        
		//If requested, gets the ID of a spot and adds it to the video
		if(!$video->is_spot && config('video.spot')) {
			$spot = $this->Videos->getRandomSpots();
			$video->spot_id = $spot[0]->youtube_id;
		}
        
		$this->set(compact('video'));
    }
    
    /**
     * Preview for videos.
     * It uses the `view` template.
     * @param string $id Video ID
	 * @uses MeYoutube\Model\Table\VideosTable::getRandomSpots()
     */
    public function preview($id = NULL) {		
		$video = $this->Videos->find()
			->select(['id', 'youtube_id', 'title', 'subtitle', 'text', 'active', 'is_spot', 'created', 'modified'])
			->contain([
                'Categories' => function($q) {
                    return $q->select(['title', 'slug']);
                },
                'Users' => function($q) {
                    return $q->select(['first_name', 'last_name']);
                },
			])
			->where([sprintf('%s.id', $this->Videos->alias()) => $id])
			->firstOrFail();
        
		//If requested, gets the ID of a spot and adds it to the video
		if(!$video->is_spot && config('video.spot')) {
			$spot = $this->Videos->getRandomSpots();
			$video->spot_id = $spot[0]->youtube_id;
		}
		
		$this->set(compact('video'));
        
        $this->render('view');
    }
}