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
use Cake\I18n\Time;
use MeYoutube\Controller\AppController;

/**
 * Videos controller
 * @property \MeYoutube\Model\Table\VideosTable $Videos
 */
class VideosController extends AppController {
	/**
     * Lists videos
	 * @uses MeYoutube\Model\Table\VideosTable::checkIfCacheIsValid()
	 */
    public function index() {
		//Checks if the cache is valid
		$this->Videos->checkIfCacheIsValid();
		
		//Sets the cache name
		$cache = sprintf('index_limit_%s_page_%s', $this->paginate['limit'], $this->request->query('page') ? $this->request->query('page') : 1);
		
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
					->where(['is_spot' => FALSE])
					->order([sprintf('%s.created', $this->Videos->alias()) => 'DESC'])
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
	 * List videos by a date
	 * @param int $year Year
	 * @param int $month Month
	 * @param int $day Day
	 * @property \MeYoutube\Model\Table\VideosTable $Videos
	 */
	public function index_by_date($year, $month, $day) {
		//Checks if the cache is valid
		$this->Videos->checkIfCacheIsValid();
		
		//Sets the cache name
		$cache = sprintf('index_date_%s_limit_%s_page_%s', md5(serialize([$year, $month, $day])), $this->paginate['limit'], $this->request->query('page') ? $this->request->query('page') : 1);
		
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
					->order([sprintf('%s.created', $this->Videos->alias()) => 'DESC'])
					->where([
						'is_spot' => FALSE,
						sprintf('%s.created >=', $this->Videos->alias()) => (new Time())->setDate($year, $month, $day)->setTime(0, 0, 0)->i18nFormat(FORMAT_FOR_MYSQL),
						sprintf('%s.created <=', $this->Videos->alias()) => (new Time())->setDate($year, $month, $day)->setTime(23, 59, 59)->i18nFormat(FORMAT_FOR_MYSQL)
					])
					->order([sprintf('%s.created', $this->Videos->alias()) => 'DESC'])
			)->toArray();
						
			//Writes on cache
			Cache::writeMany([$cache => $videos, sprintf('%s_paging', $cache) => $this->request->param('paging')], 'videos');
		}
		//Else, sets the paging parameter
		else
			$this->request->params['paging'] = $paging;
		
        $this->set(compact('videos'));
		
		$this->render('Videos/index');
	}
	
    /**
     * Views video
     * @param string $id Video ID
	 * @uses MeYoutube\Model\Table\VideosTable::checkIfCacheIsValid()
	 * @uses MeYoutube\Model\Table\VideosTable::getRandomSpots()
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function view($id = NULL) {
		//Checks if the cache is valid
		$this->Videos->checkIfCacheIsValid();
		
		$video = $this->Videos->find('active')
			->contain([
				'Categories'	=> ['fields' => ['title', 'slug']],
				'Users'			=> ['fields' => ['first_name', 'last_name']]
			])
			->select(['id', 'youtube_id', 'title', 'subtitle', 'description', 'created'])
			->where([sprintf('%s.id', $this->Videos->alias()) => $id])
			->cache(sprintf('view_%s', md5($id)), 'videos')
			->first();
		
		//If requested, gets the ID of a spot and adds it to the video
		if(config('video.spot')) {
			$spot = $this->Videos->getRandomSpots();
			$video->spot_id = $spot[0]->youtube_id;
		}
		
		$this->set(compact('video'));
    }
	
	/**
	 * Lists videos as RSS
	 * @throws \Cake\Network\Exception\ForbiddenException
	 * @uses Cake\Controller\Component\RequestHandlerComponent:isRss()
	 * @uses MeCms\Model\Table\VideosTable::checkIfCacheIsValid()
	 */
	public function rss() {
		//This method works only for RSS
		if(!$this->RequestHandler->isRss())
            throw new \Cake\Network\Exception\ForbiddenException();
		
		//Checks if the cache is valid
		$this->Videos->checkIfCacheIsValid();
		
		$this->set('videos', $this->Videos->find('active')
			->select(['id', 'youtube_id', 'title', 'description', 'created'])
			->where(['is_spot' => FALSE])
			->limit(config('frontend.records_for_rss'))
			->order([sprintf('%s.created', $this->Videos->alias()) => 'DESC'])
			->cache('rss', 'videos'));
		
		$this->viewBuilder()->layout('frontend');
	}
	
	/**
	 * Search videos
	 * @uses MeCms\Controller\Component\SecurityComponent::checkLastSearch()
	 * @uses MeCms\Model\Table\VideosTable::checkIfCacheIsValid()
	 */
	public function search() {
		if($pattern = $this->request->query('p')) {
			//Checks if the pattern is at least 4 characters long
			if(strlen($pattern) >= 4) {
				if($this->Security->checkLastSearch($pattern)) {
					$this->paginate['limit'] = config('frontend.records_for_searches');
					
					//Checks if the cache is valid
					$this->Videos->checkIfCacheIsValid();
					
					//Sets the initial cache name
					$cache = sprintf('search_%s', md5($pattern));

					//Updates the cache name with the query limit and the number of the page
					$cache = sprintf('%s_limit_%s', $cache, $this->paginate['limit']);
					$cache = sprintf('%s_page_%s', $cache, $this->request->query('page') ? $this->request->query('page') : 1);

					//Tries to get data from the cache
					list($videos, $paging) = array_values(Cache::readMany([$cache, sprintf('%s_paging', $cache)], 'videos'));

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
						Cache::writeMany([$cache => $videos, sprintf('%s_paging', $cache) => $this->request->param('paging')], 'videos');
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
	}
}