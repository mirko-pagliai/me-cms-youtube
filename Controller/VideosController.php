<?php
/**
 * VideosController.
 *
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
 * @package		MeYoutube\Controller
 */

App::uses('MeCmsAppController', 'MeCms.Controller');

/**
 * Videos Controller
 */
class VideosController extends MeCmsAppController {
	/**
	 * Check if the provided user is authorized for the request.
	 * @param array $user The user to check the authorization of. If empty the user in the session will be used.
	 * @return bool TRUE if $user is authorized, otherwise FALSE
	 * @uses isAction()
	 * @uses MeAuthComponenet::isManager()
	 * @uses MeAuthComponenet::user()
	 * @uses Video::isOwnedBy()
	 */
	public function isAuthorized($user = NULL) {
		//Only admins and managers can edit all videos
		//Users can edit only their own videos
		if($this->isAction('admin_edit') && !$this->Auth->isManager()) {
			$id = (int) $this->request->params['pass'][0];
			return $this->Video->isOwnedBy($id, $this->Auth->user('id'));
		}
		
		//Only admins and managers can delete videos
		if($this->isAction('admin_delete'))
			return $this->Auth->isManager();
		
		return TRUE;
	}

	/**
	 * List videos
	 */
	public function admin_index() {
		$this->paginate = array(
			'contain'	=> array('Category.title', 'User.first_name', 'User.last_name'),
			'fields'	=> array('id', 'title', 'priority', 'active', 'is_spot', 'created'),
			'limit'		=> $this->config['records_for_page']
		);
				
		$this->set(array(
			'videos'			=> $this->paginate(),
			'title_for_layout'	=> __d('me_youtube', 'Videos')
		));
	}

	/**
	 * Add video
	 * @uses YoutubeComponent::getId()
	 * @uses YoutubeComponent::getInfo()
	 */
	public function admin_add() {
		//Gets the categories
		$categories = $this->Video->Category->generateTreeList();
		
		//Checks for categories
		if(empty($categories)) {
			$this->Session->flash(__d('me_youtube', 'Before you can add a video, you have to create at least a category'), 'error');
			$this->redirect(array('controller' => 'videos_categories', 'action' => 'index'));
		}
		
		//Gets users
		$users = $this->Video->User->find('list', array('fields' => array('id', 'full_name')));
		
		//If the address of a YouTube video has been specified
		if(!empty($this->request->query['url']) && $this->request->is('get')) {
			//Loads the YouTube component
			$this->Youtube = $this->Components->load('MeYoutube.Youtube');
			//Gets the Youtube video ID and the video information
			$youtube_id = $this->Youtube->getId($this->request->query['url']);
			$this->request->data['Video'] = am(compact('youtube_id'), $this->Youtube->getInfo($youtube_id));
		}
		
		if($this->request->is('post')) {
			$this->Video->create();
			if($this->Video->save($this->request->data)) {
				$this->Session->flash(__d('me_youtube', 'The video has been created'));
				$this->redirect(array('action' => 'index'));
			}
			else
				$this->Session->flash(__d('me_youtube', 'The video could not be created. Please, try again'), 'error');
		}

		$this->set(am(array('title_for_layout' => __d('me_youtube', 'Add video')), compact('categories', 'users')));
	}

	/**
	 * Edit video
	 * @param string $id Video ID
	 */
	public function admin_edit($id = NULL) {
		if(!$this->Video->exists($id))
			throw new NotFoundException(__d('me_cms', 'Invalid object'));
		
		//Gets the categories
		$categories = $this->Video->Category->generateTreeList();
		
		//Gets users
		$users = $this->Video->User->find('list', array('fields' => array('id', 'full_name')));
		
		if($this->request->is('post') || $this->request->is('put')) {
			if($this->Video->save($this->request->data)) {
				$this->Session->flash(__d('me_youtube', 'The video has been edited'));
				$this->redirect(array('action' => 'index'));
			}
			else
				$this->Session->flash(__d('me_youtube', 'The video could not be edited. Please, try again'), 'error');
		} 
		else
			$this->request->data = $this->Video->find('first', array(
				'conditions'	=> array('id' => $id),
				'fields'		=> array('id', 'user_id', 'title', 'subtitle', 'youtube_id', 'category_id', 'description', 'priority', 'active', 'is_spot', 'created')
			));

		$this->set(am(array('title_for_layout' => __d('me_youtube', 'Edit video')), compact('categories', 'users')));
	}

	/**
	 * Delete video
	 * @param string $id Video ID
	 * @throws NotFoundException
	 */
	public function admin_delete($id = NULL) {
		$this->Video->id = $id;
		if(!$this->Video->exists())
			throw new NotFoundException(__d('me_cms', 'Invalid object'));
			
		$this->request->onlyAllow('post', 'delete');
		
		if($this->Video->delete())
			$this->Session->flash(__d('me_youtube', 'The video has been deleted'));
		else
			$this->Session->flash(__d('me_youtube', 'The video was not deleted'), 'error');
			
		$this->redirect(array('action' => 'index'));
	}
	
	/**
	 * Gets the latest videos.
	 * This method works only with `requestAction()`.
	 * @param int $limit Number of latest videos
	 * @return array Latest videos
	 * @throws ForbiddenException
	 * @uses isRequestAction()
	 */
	public function request_latest($limit = 1) {
		//This method works only with "requestAction()"
		if(!$this->isRequestAction())
            throw new ForbiddenException();
		
		//Tries to get data from the cache
		$videos = Cache::read($cache = sprintf('videos_request_latest_%d', $limit), 'videos');
		
		//If the data are not available from the cache
        if(empty($videos)) {
			$videos = $this->Video->find('active', array(
				'conditions'	=> array('is_spot' => FALSE),
				'fields'		=> array('id', 'youtube_id', 'title', 'description'),
				'limit'			=> $limit
			));
			
            Cache::write($cache, $videos, 'videos');
		}
		
		return $videos;
	}
	
	/**
	 * List videos
	 * @param string $category Category slug, optional
	 * @return array List of latest videos (only when requested as rss)
	 */
	public function index($category = NULL) {
		//If the videos were requested as rss
		if($this->RequestHandler->isRss()) {
			//Tries to get data from the cache
			$videos = Cache::read($cache = 'videos_rss', 'videos');

			//If the data are not available from the cache
			if(empty($videos)) {
				$videos = $this->Video->find('active', array(
					'conditions'	=> array('is_spot' => FALSE),
					'fields'		=> array('id', 'title', 'description', 'created'),
					'limit'			=> 20
				));

				Cache::write($cache, $videos, 'videos');
			}
			
			return $this->set(compact('videos'));
		}
		
		//Sets the initial cache name
		$cache = 'videos_index';
		//Sets the initial conditions query
		$conditions = array();
		
		//Checks if has been specified a category
		if(!empty($category) || !empty($this->request->query['category'])) {
			//The category can also be passed as query
			$category = empty($category) ? $this->request->query['category'] : $category;
			
			//Adds the category to the conditions, if it has been specified
			$conditions['Category.slug'] = $category;
			
			//Updates the cache name with the category name
			$cache = sprintf('%s_%s', $cache, $category);
		}
		
		//Updates the cache name with the number of the page
		$cache = sprintf('%s_page_%s', $cache, empty($this->request->named['page']) ? '1' : $this->request->named['page']);
		
		//Tries to get data from the cache
		$videos = Cache::read($cache, 'videos');
		$paging = Cache::read(sprintf('%s_paging', $cache), 'videos');
		
		//If the data are not available from the cache
		if(empty($videos) || empty($paging)) {
			$this->paginate = array(
				'contain'		=> array('Category.slug', 'Category.title', 'User.first_name', 'User.last_name'),
				'conditions'	=> am($conditions, array('is_spot' => FALSE)),
				'fields'		=> array('id', 'user_id', 'youtube_id', 'title', 'subtitle', 'description', 'created'),
				'findType'		=> 'active',
				'limit'			=> $this->config['records_for_page']
			);
			
            Cache::write($cache, $videos = $this->paginate(), 'videos');
			Cache::write(sprintf('%s_paging', $cache), $this->request->params['paging'], 'videos');
		}
		//Else, sets the paging params
		else
			$this->request->params['paging'] = $paging;
		
		//Sets the category title as the title of the layout, if it's available
		if(!empty($category) && !empty($videos[0]['Category']['title']))
			$title_for_layout = $videos[0]['Category']['title'];
		else
			$title_for_layout = __d('me_youtube', 'Videos');
		
		$this->set(compact('title_for_layout', 'videos'));
	}

	/**
	 * View video
	 * @param string $id Video ID
	 * @throws NotFoundException
	 */
	public function view($id = NULL) {
		//Tries to get data from the cache
		$video = Cache::read($cache = sprintf('videos_view_%s', $id), 'videos');
		
		//If the data are not available from the cache
		if(empty($video)) {
			if(!$this->Video->exists($id))
				throw new NotFoundException(__d('me_cms', 'Invalid object'));

			$video = $this->Video->find('active', array(
				'conditions'	=> array('Video.id' => $id),
				'contain'		=> array('Category.slug', 'Category.title', 'User.first_name', 'User.last_name'),
				'fields'		=> array('id', 'user_id', 'youtube_id', 'title', 'subtitle', 'description', 'created'),
				'limit'			=> 1
			));
			
            Cache::write($cache, $video, 'videos');
		}
		
		$this->set(am(array(
			'image_src'			=> $video['Video']['preview'],
			'title_for_layout'	=> $video['Video']['title']
		), compact('video')));
	}
	
	/**
	 * Search videos
	 */
	public function search() {
		$pattern = empty($this->request->query['p']) ? FALSE : trim($this->request->query['p']);
		
		if(!empty($pattern)) {
			//Checks if the pattern is at least 4 characters long
			if(strlen($pattern) >= 4) {
				$this->paginate = array(
					'conditions'	=> array(
						'is_spot' => FALSE,
						'OR' => array(
							'title LIKE'		=> sprintf('%%%s%%', $pattern),
							'subtitle LIKE'		=> sprintf('%%%s%%', $pattern),
							'description LIKE'	=> sprintf('%%%s%%', $pattern)
						)
					),
					'fields'		=> array('id', 'title', 'description', 'created'),
					'findType'		=> 'active',
					'limit'			=> 10
				);

				try {
					$videos = $this->paginate();
					$count = $this->request->params['paging']['Video']['count'];
				}
				catch(NotFoundException $e) {}

				$this->set(compact('count', 'videos'));
			}
			else
				$this->Session->flash(__d('me_cms', 'You have to search at least a word of %d characters', 4), 'error');
		}
		
		$this->set(am(array('title_for_layout' => __d('me_youtube', 'Search videos')), compact('pattern')));
		
	}
}