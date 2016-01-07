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
namespace MeYoutube\Controller\Admin;

use MeYoutube\Controller\AppController;

/**
 * VideosCategories controller
 * @property \MeYoutube\Model\Table\VideosCategoriesTable $VideosCategories
 */
class VideosCategoriesController extends AppController {
	/**
	 * Called before the controller action. 
	 * You can use this method to perform logic that needs to happen before each controller action.
	 * @param \Cake\Event\Event $event An Event instance
	 * @uses MeCms\Controller\AppController::beforeFilter()
	 * @uses MeYoutube\Model\Table\VideosCategoriesTable::getTreeList()
	 * @uses MeTools\Network\Request::isAction()
	 */
	public function beforeFilter(\Cake\Event\Event $event) {
		parent::beforeFilter($event);
		
		if($this->request->isAction(['add', 'edit'])) {
			//Gets and sets categories
			$this->set('categories', $categories = $this->VideosCategories->getTreeList());
		}
	}
	
	/**
	 * Checks if the provided user is authorized for the request
	 * @param array $user The user to check the authorization of. If empty the user in the session will be used
	 * @return bool TRUE if the user is authorized, otherwise FALSE
	 * @uses MeCms\Controller\AppController::isAuthorized()
	 * @uses MeCms\Controller\Component\AuthComponent::isGroup()
	 * @uses MeTools\Network\Request::isAction()
	 */
	public function isAuthorized($user = NULL) {
		//Only admins can delete videos categories
		if($this->request->isAction('delete'))
			return $this->Auth->isGroup('admin');
		
		//Admins and managers can access other actions
		return parent::isAuthorized($user);
	}
	
	/**
     * Lists videos categories
	 * @uses MeCms\Model\Table\VideosCategoriesTable::getTreeList()
     */
    public function index() {
		$categories = $this->VideosCategories->find('all')
			->contain(['Parents' => ['fields' => ['title']]])
			->order(['VideosCategories.lft' => 'ASC'])
			->select(['id', 'title', 'slug', 'video_count'])
			->toArray();
		
		//Changes the category titles, replacing them with the titles of the tree list
		array_walk($categories, function(&$category, $k, $treeList) {
			$category->title = $treeList[$category->id];
		}, $this->VideosCategories->getTreeList());
		
        $this->set(compact('categories'));
    }

    /**
     * Adds videos category
     */
    public function add() {
		$category = $this->VideosCategories->newEntity();
		
        if($this->request->is('post')) {
            $category = $this->VideosCategories->patchEntity($category, $this->request->data);
			
            if($this->VideosCategories->save($category)) {
                $this->Flash->success(__d('me_youtube', 'The videos category has been saved'));
                return $this->redirect(['action' => 'index']);
            } 
			else
                $this->Flash->error(__d('me_youtube', 'The videos category could not be saved'));
        }

        $this->set(compact('category'));
    }

    /**
     * Edits youtube videos category
     * @param string $id Videos category ID
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function edit($id = NULL)  {
		$category = $this->VideosCategories->get($id);
		
        if($this->request->is(['patch', 'post', 'put'])) {
            $category = $this->VideosCategories->patchEntity($category, $this->request->data);
			
            if($this->VideosCategories->save($category)) {
                $this->Flash->success(__d('me_youtube', 'The videos category has been saved'));
                return $this->redirect(['action' => 'index']);
            } 
			else
                $this->Flash->error(__d('me_youtube', 'The videos category could not be saved'));
        }

        $this->set(compact('category'));
    }
	
    /**
     * Deletes videos category
     * @param string $id Videos category ID
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function delete($id = NULL) {
        $this->request->allowMethod(['post', 'delete']);
		
        $category = $this->VideosCategories->get($id);
		
		//Before deleting, it checks if the category has some videos
		if(!$category->video_count) {
			if($this->VideosCategories->delete($category))
				$this->Flash->success(__d('me_youtube', 'The videos category has been deleted'));
			else
				$this->Flash->error(__d('me_youtube', 'The videos category could not be deleted'));
		}
		else
			$this->Flash->alert(__d('me_youtube', 'Before you delete this category, you have to delete its videos or assign them to another category'));
		
        return $this->redirect(['action' => 'index']);
    }
}