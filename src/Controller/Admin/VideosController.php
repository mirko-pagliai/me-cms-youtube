<?php
/**
 * This file is part of me-cms-youtube.
 *
 * me-cms-youtube is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * me-cms-youtube is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with me-cms-youtube.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeCmsYoutube\Controller\Admin;

use Cake\I18n\Time;
use MeCmsYoutube\Controller\AppController;
use MeCmsYoutube\Utility\Youtube;

/**
 * Videos controller
 * @property \MeCmsYoutube\Model\Table\VideosTable $Videos
 */
class VideosController extends AppController
{
    /**
     * Called before the controller action.
     * You can use this method to perform logic that needs to happen before
     *  each controller action.
     * @param \Cake\Event\Event $event An Event instance
     * @return void
     * @uses MeCms\Controller\AppController::beforeFilter()
     * @uses MeCms\Model\Table\VideosCategoriesTable::getList()
     * @uses MeCms\Model\Table\VideosCategoriesTable::getTreeList()
     * @uses MeCms\Model\Table\UsersTable::getActiveList()
     * @uses MeCms\Model\Table\UsersTable::getList()
     */
    public function beforeFilter(\Cake\Event\Event $event)
    {
        parent::beforeFilter($event);

        if ($this->request->isIndex()) {
            $categories = $this->Videos->Categories->getList();
            $users = $this->Videos->Users->getList();
        } elseif ($this->request->isAction(['add', 'edit'])) {
            $categories = $this->Videos->Categories->getTreeList();
            $users = $this->Videos->Users->getActiveList();
        }

        //Checks for categories
        if (isset($categories) && empty($categories)) {
            $this->Flash->alert(__d('me_cms', 'You must first create a category'));
            $this->redirect(['controller' => 'VideosCategories', 'action' => 'index']);
        }

        if (!empty($categories)) {
            $this->set(compact('categories'));
        }

        if (!empty($users)) {
            $this->set(compact('users'));
        }
    }

    /**
     * Check if the provided user is authorized for the request
     * @param array $user The user to check the authorization of. If empty
     * the user in the session will be used
     * @return bool `true` if the user is authorized, otherwise `false`
     * @uses MeCms\Controller\Component\AuthComponent::isGroup()
     * @uses MeCms\Model\Table\AppTable::isOwnedBy()
     */
    public function isAuthorized($user = null)
    {
        //Only admins and managers can edit all videos.
        //Users can edit only their own videos
        if ($this->request->isEdit() && !$this->Auth->isGroup(['admin', 'manager'])) {
            return $this->Videos->isOwnedBy($this->request->pass[0], $this->Auth->user('id'));
        }

        //Only admins and managers can delete videos
        if ($this->request->isDelete()) {
            return $this->Auth->isGroup(['admin', 'manager']);
        }

        return true;
    }

    /**
     * Lists videos
     * @return void
     * @uses MeCmsYoutube\Model\Table\VideosTable::queryFromFilter()
     */
    public function index()
    {
        $query = $this->Videos->find()->contain([
            'Categories' => function ($q) {
                return $q->select(['id', 'title']);
            },
            'Users' => function ($q) {
                return $q->select(['id', 'first_name', 'last_name']);
            },
        ]);

        $this->paginate['order'] = ['created' => 'DESC'];

        $videos = $this->paginate($this->Videos->queryFromFilter($query, $this->request->query));

        $this->set(compact('videos'));
    }

    /**
     * Adds video
     * @return \Cake\Network\Response|null|void
     * @uses MeCmsYoutube\Utility\Youtube::getId()
     * @uses MeCmsYoutube\Utility\Youtube::getInfo()
     * @uses MeCmsYoutube\Utility\Youtube::getUrl()
     */
    public function add()
    {
        $youtube = new Youtube;

        //If the address of a YouTube video has been specified
        if ($this->request->query('url') && $this->request->is('get')) {
            //Gets video ID and information
            $youtubeId = $youtube->getId($this->request->query('url'));
            $youtubeInfo = $youtube->getInfo($youtubeId);
            $youtubeUrl = $youtube->getUrl($youtubeId);

            if (!$youtubeId) {
                $this->Flash->error(__d('me_cms_youtube', 'This is not a {0} video', 'YouTube'));
            } elseif (!$youtubeInfo || !$youtubeUrl) {
                $this->Flash->error(__d('me_cms_youtube', 'Unable to retrieve video informations. Probably the video is private'));
            } else {
                $this->request->data = am([
                    'youtube_id' => $youtubeId,
                    'youtube_url' => $youtubeUrl,
                ], $youtubeInfo);
            }
        }

        $video = $this->Videos->newEntity();

        if ($this->request->is('post')) {
            //Only admins and managers can add videos on behalf of other users
            if (!$this->Auth->isGroup(['admin', 'manager'])) {
                $this->request->data('user_id', $this->Auth->user('id'));
            }

            $this->request->data['created'] = new Time($this->request->data('created'));

            $video = $this->Videos->patchEntity($video, $this->request->data);

            if ($this->Videos->save($video)) {
                $this->Flash->success(__d('me_cms', 'The operation has been performed correctly'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('me_cms', 'The operation has not been performed correctly'));
            }

            //Gets video ID and information
            $youtubeId = $youtube->getId($this->request->query('url'));
            $youtubeInfo = $youtube->getInfo($youtubeId);
            $youtubeUrl = $youtube->getUrl($youtubeId);

            $this->request->data = am([
                'youtube_id' => $youtubeId,
                'youtube_url' => $youtubeUrl,
            ], $youtubeInfo, $this->request->data);
        }

        $this->set(compact('video'));
    }

    /**
     * Edits video
     * @param string $id Video ID
     * @return \Cake\Network\Response|null|void
     */
    public function edit($id = null)
    {
        $video = $this->Videos->get($id);

        if ($this->request->is(['patch', 'post', 'put'])) {
            //Only admins and managers can edit videos on behalf of other users
            if (!$this->Auth->isGroup(['admin', 'manager'])) {
                $this->request->data('user_id', $this->Auth->user('id'));
            }

            $this->request->data['created'] = new Time($this->request->data('created'));

            $video = $this->Videos->patchEntity($video, $this->request->data);

            if ($this->Videos->save($video)) {
                $this->Flash->success(__d('me_cms', 'The operation has been performed correctly'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('me_cms', 'The operation has not been performed correctly'));
            }
        }

        $this->set(compact('video'));
    }
    /**
     * Deletes video
     * @param string $id Video ID
     * @return \Cake\Network\Response|null
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $video = $this->Videos->get($id);

        if ($this->Videos->delete($video)) {
            $this->Flash->success(__d('me_cms', 'The operation has been performed correctly'));
        } else {
            $this->Flash->error(__d('me_cms', 'The operation has not been performed correctly'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
