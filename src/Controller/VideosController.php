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

use MeTools\Cache\Cache;
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
	 * @uses MeTools\Network\Request::isAction()
	 */
	public function beforeFilter(\Cake\Event\Event $event) {
        parent::beforeFilter($event);
        
        //View videos. It checks created datetime and status. Logged users can view future objects and drafts
		if($this->request->isAction('view')) {
            if($this->Auth->user()) {
                return;
            }
            
            $id = $this->request->param('id');
                        
            $video = $this->Videos->find()
                ->select(['active', 'created'])
                ->where(compact('id'))
                ->cache(sprintf('status_%s', md5($id)), $this->Videos->cache)
                ->firstOrFail();
            
            if($video->active && $video->created->isPast()) {
                return;
            }
            
            $this->Auth->deny('view');
        }
    }
    
	/**
     * Lists videos
	 */
    public function index() {
		//Sets the cache name
		$cache = sprintf('index_limit_%s_page_%s', $this->paginate['limit'], $this->request->query('page') ? $this->request->query('page') : 1);
		
		//Tries to get data from the cache
		list($videos, $paging) = array_values(Cache::readMany([$cache, sprintf('%s_paging', $cache)], $this->Videos->cache));
		
		//If the data are not available from the cache
		if(empty($videos) || empty($paging)) {
			$videos = $this->paginate(
				$this->Videos->find('active')
					->contain([
						'Categories'	=> ['fields' => ['title', 'slug']],
						'Users'			=> ['fields' => ['first_name', 'last_name']]
					])
					->select(['id', 'youtube_id', 'title', 'subtitle', 'description', 'created'])
					->where(['is_spot' => FALSE])
					->order([sprintf('%s.created', $this->Videos->alias()) => 'DESC'])
			)->toArray();
						
			//Writes on cache
			Cache::writeMany([$cache => $videos, sprintf('%s_paging', $cache) => $this->request->param('paging')], $this->Videos->cache);
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
	 * List videos by a date
	 * @param int $year Year
	 * @param int $month Month
	 * @param int $day Day
	 * @property \MeYoutube\Model\Table\VideosTable $Videos
	 */
	public function index_by_date($year, $month, $day) {		
		//Sets the cache name
		$cache = sprintf('index_date_%s_limit_%s_page_%s', md5(serialize([$year, $month, $day])), $this->paginate['limit'], $this->request->query('page') ? $this->request->query('page') : 1);
		
		//Tries to get data from the cache
		list($videos, $paging) = array_values(Cache::readMany([$cache, sprintf('%s_paging', $cache)], $this->Videos->cache));
		
		//If the data are not available from the cache
		if(empty($videos) || empty($paging)) {		
			$videos = $this->paginate(
				$this->Videos->find('active')
					->contain([
						'Categories'	=> ['fields' => ['title', 'slug']],
						'Users'			=> ['fields' => ['first_name', 'last_name']]
					])
					->select(['id', 'youtube_id', 'title', 'subtitle', 'description', 'created'])
					->order([sprintf('%s.created', $this->Videos->alias()) => 'DESC'])
					->where([
						'is_spot' => FALSE,
						sprintf('%s.created >=', $this->Videos->alias()) => (new Time())->setDate($year, $month, $day)->setTime(0, 0, 0)->i18nFormat(FORMAT_FOR_MYSQL),
						sprintf('%s.created <=', $this->Videos->alias()) => (new Time())->setDate($year, $month, $day)->setTime(23, 59, 59)->i18nFormat(FORMAT_FOR_MYSQL)
					])
					->order([sprintf('%s.created', $this->Videos->alias()) => 'DESC'])
			)->toArray();
						
			//Writes on cache
			Cache::writeMany([$cache => $videos, sprintf('%s_paging', $cache) => $this->request->param('paging')], $this->Videos->cache);
		}
		//Else, sets the paging parameter
		else
			$this->request->params['paging'] = $paging;
		
        $this->set(compact('videos'));
		
		$this->render('Videos/index');
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
     * Views video
     * @param string $id Video ID
	 * @uses MeYoutube\Model\Table\VideosTable::getRandomSpots()
     */
    public function view($id = NULL) {		
		$video = $this->Videos->find()
			->contain([
				'Categories'	=> ['fields' => ['title', 'slug']],
				'Users'			=> ['fields' => ['first_name', 'last_name']]
			])
			->select(['id', 'youtube_id', 'title', 'subtitle', 'description', 'active', 'created', 'modified'])
			->where([sprintf('%s.id', $this->Videos->alias()) => $id])
			->cache(sprintf('view_%s', md5($id)), $this->Videos->cache)
			->firstOrFail();
		
		$this->set(compact('video'));
        
		//If requested, gets the ID of a spot and adds it to the video
		if(config('video.spot')) {
			$spot = $this->Videos->getRandomSpots();
			$video->spot_id = $spot[0]->youtube_id;
		}
    }
	
	/**
	 * Lists videos as RSS
	 * @throws \Cake\Network\Exception\ForbiddenException
	 * @uses Cake\Controller\Component\RequestHandlerComponent:isRss()
	 */
	public function rss() {
		//This method works only for RSS
		if(!$this->RequestHandler->isRss())
            throw new \Cake\Network\Exception\ForbiddenException();
		
		$this->set('videos', $this->Videos->find('active')
			->select(['id', 'youtube_id', 'title', 'description', 'created'])
			->where(['is_spot' => FALSE])
			->limit(config('frontend.records_for_rss'))
			->order([sprintf('%s.created', $this->Videos->alias()) => 'DESC'])
			->cache('rss', $this->Videos->cache));
		
		$this->viewBuilder()->layout('MeCms.frontend');
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
					$this->paginate['limit'] = config('frontend.records_for_searches');
					
					//Sets the initial cache name
					$cache = sprintf('search_%s', md5($pattern));

					//Updates the cache name with the query limit and the number of the page
					$cache = sprintf('%s_limit_%s', $cache, $this->paginate['limit']);
					$cache = sprintf('%s_page_%s', $cache, $this->request->query('page') ? $this->request->query('page') : 1);

					//Tries to get data from the cache
					list($videos, $paging) = array_values(Cache::readMany([$cache, sprintf('%s_paging', $cache)], $this->Videos->cache));

					//If the data are not available from the cache
					if(empty($videos) || empty($paging)) {
						$videos = $this->paginate(
							$this->Videos->find('active')
								->select(['id', 'title', 'description', 'created'])
								->where(['OR' => [
									'title LIKE'		=> sprintf('%%%s%%', $pattern),
									'subtitle LIKE'		=> sprintf('%%%s%%', $pattern),
									'description LIKE'	=> sprintf('%%%s%%', $pattern)
								]])
								->order([sprintf('%s.created', $this->Videos->alias()) => 'DESC'])
						)->toArray();

						//Writes on cache
						Cache::writeMany([$cache => $videos, sprintf('%s_paging', $cache) => $this->request->param('paging')], $this->Videos->cache);
					}
					//Else, sets the paging parameter
					else
						$this->request->params['paging'] = $paging;

					$this->set(compact('videos'));
				}
				else
					$this->Flash->alert(__d('me_cms', 'You have to wait {0} seconds to perform a new search', config('security.search_interval')));
			}
			else
				$this->Flash->alert(__d('me_cms', 'You have to search at least a word of {0} characters', 4));
		}
        
        $this->set(compact('pattern'));
	}
}