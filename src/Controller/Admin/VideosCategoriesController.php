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

/**
 * VideosCategories controller
 */
class VideosCategoriesController extends AppController {
	/**
     * Lists videos categories
     */
    public function index() {
        $this->paginate = [
            'contain' => ['ParentVideosCategories']
        ];
        $this->set('videosCategories', $this->paginate($this->VideosCategories));
    }

    /**
     * Adds videos category
     */
    public function add() {
        $videosCategory = $this->VideosCategories->newEntity();
		
        if($this->request->is('post')) {
            $videosCategory = $this->VideosCategories->patchEntity($videosCategory, $this->request->data);
			
            if($this->VideosCategories->save($videosCategory)) {
                $this->Flash->success(__('The videos category has been saved'));
				return $this->redirect(['action' => 'index']);
            } 
			else
                $this->Flash->error(__('The videos category could not be saved'));
        }
        $parentVideosCategories = $this->VideosCategories->ParentVideosCategories->find('list', ['limit' => 200]);

        $this->set(compact('videosCategory', 'parentVideosCategories'));
    }

    /**
     * Edits youtube videos category
     * @param string $id Videos Category ID
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function edit($id = NULL)  {
        $videosCategory = $this->VideosCategories->get($id, [
            'contain' => []
        ]);
		
        if($this->request->is(['patch', 'post', 'put'])) {
            $videosCategory = $this->VideosCategories->patchEntity($videosCategory, $this->request->data);
			
            if($this->VideosCategories->save($videosCategory)) {
                $this->Flash->success(__('The videos category has been saved'));
                return $this->redirect(['action' => 'index']);
            } 
			else
                $this->Flash->error(__('The videos category could not be saved'));
        }
        $parentVideosCategories = $this->VideosCategories->ParentVideosCategories->find('list', ['limit' => 200]);

        $this->set(compact('videosCategory', 'parentVideosCategories'));
    }
    /**
     * Deletes youtube videos category
     * @param string $id Videos Category ID
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function delete($id = NULL) {
        $this->request->allowMethod(['post', 'delete']);
		
        $videosCategory = $this->VideosCategories->get($id);
		
        if($this->VideosCategories->delete($videosCategory))
            $this->Flash->success(__('The videos category has been deleted'));
        else
            $this->Flash->error(__('The videos category could not be deleted'));
			
        return $this->redirect(['action' => 'index']);
    }
}