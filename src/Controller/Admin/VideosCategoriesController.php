<?php
namespace MeYoutube\Controller\Admin;

use MeYoutube\Controller\AppController;

/**
 * VideosCategories controller
 */
class VideosCategoriesController extends AppController {
	/**
     * Lists youtubeVideosCategories
     */
    public function index() {
        $this->paginate = [
            'contain' => ['ParentYoutubeVideosCategories']
        ];
        $this->set('youtubeVideosCategories', $this->paginate($this->YoutubeVideosCategories));
    }

    /**
     * Adds youtube videos category
     */
    public function add() {
        $youtubeVideosCategory = $this->YoutubeVideosCategories->newEntity();
		
        if($this->request->is('post')) {
            $youtubeVideosCategory = $this->YoutubeVideosCategories->patchEntity($youtubeVideosCategory, $this->request->data);
			
            if($this->YoutubeVideosCategories->save($youtubeVideosCategory)) {
                $this->Flash->success(__('The youtube videos category has been saved'));
				return $this->redirect(['action' => 'index']);
            } 
			else
                $this->Flash->error(__('The youtube videos category could not be saved'));
        }
        $parentYoutubeVideosCategories = $this->YoutubeVideosCategories->ParentYoutubeVideosCategories->find('list', ['limit' => 200]);

        $this->set(compact('youtubeVideosCategory', 'parentYoutubeVideosCategories'));
    }

    /**
     * Edits youtube videos category
     * @param string $id Youtube Videos Category ID
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function edit($id = NULL)  {
        $youtubeVideosCategory = $this->YoutubeVideosCategories->get($id, [
            'contain' => []
        ]);
		
        if($this->request->is(['patch', 'post', 'put'])) {
            $youtubeVideosCategory = $this->YoutubeVideosCategories->patchEntity($youtubeVideosCategory, $this->request->data);
			
            if($this->YoutubeVideosCategories->save($youtubeVideosCategory)) {
                $this->Flash->success(__('The youtube videos category has been saved'));
                return $this->redirect(['action' => 'index']);
            } 
			else
                $this->Flash->error(__('The youtube videos category could not be saved'));
        }
        $parentYoutubeVideosCategories = $this->YoutubeVideosCategories->ParentYoutubeVideosCategories->find('list', ['limit' => 200]);

        $this->set(compact('youtubeVideosCategory', 'parentYoutubeVideosCategories'));
    }
    /**
     * Deletes youtube videos category
     * @param string $id Youtube Videos Category ID
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function delete($id = NULL) {
        $this->request->allowMethod(['post', 'delete']);
		
        $youtubeVideosCategory = $this->YoutubeVideosCategories->get($id);
		
        if($this->YoutubeVideosCategories->delete($youtubeVideosCategory))
            $this->Flash->success(__('The youtube videos category has been deleted'));
        else
            $this->Flash->error(__('The youtube videos category could not be deleted'));
			
        return $this->redirect(['action' => 'index']);
    }
}