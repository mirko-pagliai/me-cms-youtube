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

use Cake\Event\Event;
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
     * @return \Cake\Network\Response|null|void
     * @uses MeCms\Controller\AppController::beforeFilter()
     * @uses MeCms\Model\Table\UsersTable::getActiveList()
     * @uses MeCms\Model\Table\UsersTable::getList()
     * @uses MeCmsYoutube\Model\Table\VideosCategoriesTable::getList()
     * @uses MeCmsYoutube\Model\Table\VideosCategoriesTable::getTreeList()
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        if ($this->request->isAction(['add', 'edit'])) {
            $categories = $this->Videos->Categories->getTreeList();
            $users = $this->Videos->Users->getActiveList();
        } else {
            $categories = $this->Videos->Categories->getList();
            $users = $this->Videos->Users->getList();
        }

        if (!$users) {
            $this->Flash->alert(__d('me_cms', 'You must first create an user'));

            return $this->redirect(['controller' => 'Users', 'action' => 'index']);
        }

        if (!$categories) {
            $this->Flash->alert(__d('me_cms', 'You must first create a category'));

            return $this->redirect(['controller' => 'VideosCategories', 'action' => 'index']);
        }

        $this->set(compact('categories', 'users'));
    }

    /**
     * Check if the provided user is authorized for the request
     * @param array $user The user to check the authorization of. If empty
     * the user in the session will be used
     * @return bool `true` if the user is authorized, otherwise `false`
     * @uses MeCms\Controller\Component\AuthComponent::isGroup()
     * @uses MeCms\Model\Table\Traits\IsOwnedByTrait::isOwnedBy()
     */
    public function isAuthorized($user = null)
    {
        //Only admins and managers can edit all videos.
        //Users can edit only their own videos
        if ($this->request->isEdit()) {
            return $this->Auth->isGroup(['admin', 'manager']) ||
                $this->Videos->isOwnedBy($this->request->getParam('pass.0'), $this->Auth->user('id'));
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
        $query = $this->Videos->find()
            ->contain([
                'Categories' => ['fields' => ['id', 'title']],
                'Users' => ['fields' => ['id', 'first_name', 'last_name']],
            ]);

        $this->paginate['order'] = ['created' => 'DESC'];

        $videos = $this->paginate($this->Videos->queryFromFilter($query, $this->request->getQuery()));

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

        //Gets and sets (as request data) the YouTube ID and url
        if ($this->request->getQuery('url')) {
            $youtubeId = $youtube->getId($this->request->getQuery('url'));

            if (!$youtubeId) {
                $this->Flash->error(__d('me_cms_youtube', 'This is not a {0} video', 'YouTube'));

                return $this->redirect([]);
            }

            $youtubeUrl = $youtube->getUrl($youtubeId);

            $this->request = $this->request->withData('youtube_id', $youtubeId)
                ->withData('youtube_url', $youtubeUrl);
        }

        //Gets and sets (as request data) information about the video. This only
        //  happens with a `GET` request, so if the form has not yet been sended
        if (!empty($youtubeId) && $this->request->is('get')) {
            //Gets video ID and information
            $youtubeInfo = $youtube->getInfo($youtubeId);

            if ($youtubeInfo) {
                foreach ($youtubeInfo as $key => $value) {
                    $this->request = $this->request->withData($key, $value);
                }
            } else {
                $this->Flash->error(__d('me_cms_youtube', 'Unable to retrieve video informations. Probably the video is private'));

                return $this->redirect([]);
            }
        }

        $video = $this->Videos->newEntity();

        if ($this->request->is('post')) {
            //Only admins and managers can add videos on behalf of other users
            if (!$this->Auth->isGroup(['admin', 'manager'])) {
                $this->request = $this->request->withData('user_id', $this->Auth->user('id'));
            }

            $video = $this->Videos->patchEntity($video, $this->request->getData());

            if ($this->Videos->save($video)) {
                $this->Flash->success(__d('me_cms', 'The operation has been performed correctly'));

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__d('me_cms', 'The operation has not been performed correctly'));

            //Gets and sets (as request data) again information about the video,
            // if these are no longer present in the request data
            $youtubeInfo = $youtube->getInfo($youtubeId);

            foreach ($youtubeInfo as $key => $value) {
                if (!$this->request->getData($key)) {
                    $this->request = $this->request->withData($key, $value);
                }
            }
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
        $video = $this->Videos->findById($id)
            ->formatResults(function ($results) {
                return $results->map(function ($row) {
                    $row->created = $row->created->i18nFormat(FORMAT_FOR_MYSQL);

                    return $row;
                });
            })
            ->firstOrFail();

        if ($this->request->is(['patch', 'post', 'put'])) {
            //Only admins and managers can edit videos on behalf of other users
            if (!$this->Auth->isGroup(['admin', 'manager'])) {
                $this->request = $this->request->withData('user_id', $this->Auth->user('id'));
            }

            $video = $this->Videos->patchEntity($video, $this->request->getData());

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
