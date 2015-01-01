<?php
/**
 * VideosCategoriesController.
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
 * VideosCategories Controller
 */
class VideosCategoriesController extends MeCmsAppController {	
	/**
	 * Check if the provided user is authorized for the request.
	 * @param array $user The user to check the authorization of. If empty the user in the session will be used.
	 * @return bool TRUE if $user is authorized, otherwise FALSE
	 * @uses MeAuthComponenet::isManager()
	 */
	public function isAuthorized($user = NULL) {
		//Only admins and managers can access this controller
		return $this->Auth->isManager();
	}
	
	/**
	 * List categories
	 */
	public function admin_index() {
		//Gets the categories
		$categories = $this->VideosCategory->find('all', array(
			'contain'	=> 'Parent.title',
			'fields'	=> array('id', 'slug', 'video_count')
		));
		
		//Changes the category titles, replacing them with the titles of the tree list
		array_walk($categories, function(&$v, $k, $treeList) {
			$v['VideosCategory']['title'] = $treeList[$v['VideosCategory']['id']];
		}, $this->VideosCategory->generateTreeList());
		
		$this->set(array(
			'categories'		=> $categories,
			'title_for_layout'	=> __d('me_youtube', 'Videos categories'))
		);
	}

	/**
	 * Add category
	 */
	public function admin_add() {
		if($this->request->is('post')) {
			$this->VideosCategory->create();
			if($this->VideosCategory->save($this->request->data)) {
				$this->Session->flash(__d('me_youtube', 'The videos category has been created'));
				$this->redirect(array('action' => 'index'));
			}
			else
				$this->Session->flash(__d('me_youtube', 'The videos category could not be created. Please, try again'), 'error');
		}
		
		$this->set(array(
			'parents'			=> $this->VideosCategory->generateTreeList(),
			'title_for_layout'	=> __d('me_youtube', 'Add videos category')
		));
	}

	/**
	 * Edit category
	 * @param string $id Category id
	 * @throws NotFoundException
	 */
	public function admin_edit($id = NULL) {
		if(!$this->VideosCategory->exists($id))
			throw new NotFoundException(__d('me_cms', 'Invalid object'));
			
		if($this->request->is('post') || $this->request->is('put')) {
			if($this->VideosCategory->save($this->request->data)) {
				$this->Session->flash(__d('me_youtube', 'The videos category has been edited'));
				$this->redirect(array('action' => 'index'));
			}
			else
				$this->Session->flash(__d('me_youtube', 'The videos category could not be edited. Please, try again'), 'error');
		} 
		else
			$this->request->data = $this->VideosCategory->find('first', array(
				'conditions'	=> array('id' => $id),
				'fields'		=> array('id', 'parent_id', 'title', 'slug', 'description')
			));

		$this->set(array(
			'categories'		=> $this->VideosCategory->generateTreeList(),
			'title_for_layout'	=> __d('me_youtube', 'Edit videos category')
		));
	}

	/**
	 * Delete category
	 * @param string $id Category id
	 * @throws NotFoundException
	 */
	public function admin_delete($id = NULL) {
		$this->VideosCategory->id = $id;
		if(!$this->VideosCategory->exists())
			throw new NotFoundException(__d('me_cms', 'Invalid object'));
			
		$this->request->onlyAllow('post', 'delete');
		
		//Before deleting, it checks if the category has some videos
		if(!$this->VideosCategory->field('video_count')) {
			if($this->VideosCategory->delete())
				$this->Session->flash(__d('me_youtube', 'The videos category has been deleted'));
			else
				$this->Session->flash(__d('me_youtube', 'The videos category was not deleted'), 'error');
		}
		else
			$this->Session->flash(__d('me_youtube', 'Before you delete this category, you have to delete its videos or assign them to another category'), 'error');
		
		$this->redirect(array('action' => 'index'));
	}
	
	/**
	 * List categories
	 */
	public function index() {		
		//Tries to get data from the cache
		$categories = Cache::read($cache = 'videos_categories_index', 'videos');
		
		//If the data are not available from the cache
        if(empty($categories)) {
			$categories = $this->VideosCategory->find('active', array('fields' => array('title', 'slug')));
			
            Cache::write($cache, $categories, 'videos');
        }
		
		$this->set(am(array('title_for_layout' => __d('me_youtube', 'Videos categories')), compact('categories')));
	}
}