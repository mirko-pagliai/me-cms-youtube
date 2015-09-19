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
namespace MeYoutube\Controller\Admin;

use MeCms\Controller\AppController;
use MeYoutube\Utility\Youtube;

/**
 * Videos controller
 */
class VideosController extends AppController {
	/**
	 * Called before the controller action. 
	 * You can use this method to perform logic that needs to happen before each controller action.
	 * @param \Cake\Event\Event $event An Event instance
	 * @uses MeCms\Controller\AppController::beforeFilter()
	 * @uses MeCms\Model\Table\VideosCategoriesTable::getList()
	 * @uses MeCms\Model\Table\VideosCategoriesTable::getTreeList()
	 * @uses MeCms\Model\Table\UsersTable::getActiveList()
	 * @uses MeCms\Model\Table\UsersTable::getList()
	 * @uses MeTools\Network\Request::isAction()
	 */
	public function beforeFilter(\Cake\Event\Event $event) {
		parent::beforeFilter($event);
		
		if($this->request->isAction('index')) {
			$categories = $this->Videos->Categories->getList();
			$users = $this->Videos->Users->getList();
		}
		elseif($this->request->isAction(['add', 'edit'])) {
			$categories = $this->Videos->Categories->getTreeList();
			$users = $this->Videos->Users->getActiveList();
		}
		
		//Checks for categories
		if(isset($categories) && empty($categories)) {
			$this->Flash->alert(__d('me_youtube', 'Before you can manage videos, you have to create at least a category'));
			$this->redirect(['controller' => 'VideosCategories', 'action' => 'index']);
		}
		
		if(!empty($categories))
			$this->set(compact('categories'));
		
		if(!empty($users))
			$this->set(compact('users'));
	}
	
	/**
     * Lists videos
     */
    public function index() {
		$this->set('videos', $this->paginate(
			$this->Videos->find()
				->contain([
					'Categories'	=> ['fields' => ['title']],
					'Users'			=> ['fields' => ['first_name', 'last_name']]
				])
				->select(['id', 'title', 'priority', 'active', 'created', 'is_spot'])
				->where($this->Videos->fromFilter($this->request->query))
				->order(['Videos.created' => 'DESC'])
		));
    }

    /**
     * Adds video
	 * @uses MeYoutube\Utility\Youtube::getId()
	 * @uses MeYoutube\Utility\Youtube::getInfo()
     */
    public function add() {
		//If the address of a YouTube video has been specified
		if($this->request->query('url') && $this->request->is('get')) {
			//Checks for the Youtube video ID and the video information	
			if(!$youtube_id = Youtube::getId($this->request->query('url')))
				$this->Flash->error(__d('me_youtube', 'This is not a {0} video', 'YouTube'));
				
			$this->request->data = am(compact('youtube_id'),  Youtube::getInfo($youtube_id));
		}
		
        $video = $this->Videos->newEntity();
		
        if($this->request->is('post')) {
			//Only admins and managers can add posts on behalf of other users
			if(!$this->Auth->isGroup(['admin', 'manager']))
				$this->request->data('user_id', $this->Auth->user('id'));
			
			$this->request->data['created'] = new Time($this->request->data('created'));
			
            $video = $this->Videos->patchEntity($video, $this->request->data);
			
            if($this->Videos->save($video)) {
                $this->Flash->success(__d('me_youtube', 'The video has been saved'));
				return $this->redirect(['action' => 'index']);
            } 
			else
                $this->Flash->error(__d('me_youtube', 'The video could not be saved'));
        }

        $this->set(compact('video'));
    }

    /**
     * Edits video
     * @param string $id Video ID
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function edit($id = NULL)  {
        $video = $this->Videos->get($id);
		
        if($this->request->is(['patch', 'post', 'put'])) {
			//Only admins and managers can edit posts on behalf of other users
			if(!$this->Auth->isGroup(['admin', 'manager']))
				$this->request->data('user_id', $this->Auth->user('id'));
			
			$this->request->data['created'] = new Time($this->request->data('created'));
			
            $video = $this->Videos->patchEntity($video, $this->request->data);
			
            if($this->Videos->save($video)) {
                $this->Flash->success(__d('me_youtube', 'The video has been saved'));
                return $this->redirect(['action' => 'index']);
            } 
			else
                $this->Flash->error(__d('me_youtube', 'The video could not be saved'));
        }

        $this->set(compact('video'));
    }
    /**
     * Deletes video
     * @param string $id Video ID
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function delete($id = NULL) {
        $this->request->allowMethod(['post', 'delete']);
		
        $video = $this->Videos->get($id);
		
        if($this->Videos->delete($video))
            $this->Flash->success(__d('me_youtube', 'The video has been deleted'));
        else
            $this->Flash->error(__d('me_youtube', 'The video could not be deleted'));
			
        return $this->redirect(['action' => 'index']);
    }
}