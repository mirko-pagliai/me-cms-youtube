<?php
namespace MeYoutube\Controller\Admin;

use MeYoutube\Controller\AppController;

/**
 * Videos controller
 */
class VideosController extends AppController {
	/**
     * Lists youtubeVideos
     */
    public function index() {
        $this->paginate = [
            'contain' => ['Users', 'Youtubes', 'Categories']
        ];
        $this->set('youtubeVideos', $this->paginate($this->YoutubeVideos));
    }

    /**
     * Adds youtube video
     */
    public function add() {
        $youtubeVideo = $this->YoutubeVideos->newEntity();
		
        if($this->request->is('post')) {
            $youtubeVideo = $this->YoutubeVideos->patchEntity($youtubeVideo, $this->request->data);
			
            if($this->YoutubeVideos->save($youtubeVideo)) {
                $this->Flash->success(__('The youtube video has been saved'));
				return $this->redirect(['action' => 'index']);
            } 
			else
                $this->Flash->error(__('The youtube video could not be saved'));
        }
        $users = $this->YoutubeVideos->Users->find('list', ['limit' => 200]);
        $youtubes = $this->YoutubeVideos->Youtubes->find('list', ['limit' => 200]);
        $categories = $this->YoutubeVideos->Categories->find('list', ['limit' => 200]);

        $this->set(compact('youtubeVideo', 'users', 'youtubes', 'categories'));
    }

    /**
     * Edits youtube video
     * @param string $id Youtube Video ID
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function edit($id = NULL)  {
        $youtubeVideo = $this->YoutubeVideos->get($id, [
            'contain' => []
        ]);
		
        if($this->request->is(['patch', 'post', 'put'])) {
            $youtubeVideo = $this->YoutubeVideos->patchEntity($youtubeVideo, $this->request->data);
			
            if($this->YoutubeVideos->save($youtubeVideo)) {
                $this->Flash->success(__('The youtube video has been saved'));
                return $this->redirect(['action' => 'index']);
            } 
			else
                $this->Flash->error(__('The youtube video could not be saved'));
        }
        $users = $this->YoutubeVideos->Users->find('list', ['limit' => 200]);
        $youtubes = $this->YoutubeVideos->Youtubes->find('list', ['limit' => 200]);
        $categories = $this->YoutubeVideos->Categories->find('list', ['limit' => 200]);

        $this->set(compact('youtubeVideo', 'users', 'youtubes', 'categories'));
    }
    /**
     * Deletes youtube video
     * @param string $id Youtube Video ID
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function delete($id = NULL) {
        $this->request->allowMethod(['post', 'delete']);
		
        $youtubeVideo = $this->YoutubeVideos->get($id);
		
        if($this->YoutubeVideos->delete($youtubeVideo))
            $this->Flash->success(__('The youtube video has been deleted'));
        else
            $this->Flash->error(__('The youtube video could not be deleted'));
			
        return $this->redirect(['action' => 'index']);
    }
}