<?php
/**
 * This file is part of me-cms-youtube.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-cms-youtube
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace MeCmsYoutube\Controller\Admin;

use Cake\Event\Event;
use Cake\ORM\ResultSet;
use MeCmsYoutube\Controller\AppController;
use MeCmsYoutube\Model\Entity\Video;
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

        if ($users->isEmpty()) {
            $this->Flash->alert(__d('me_cms', 'You must first create an user'));

            return $this->redirect(['controller' => 'Users', 'action' => 'index']);
        }

        if ($categories->isEmpty()) {
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
                $this->Flash->success(I18N_OPERATION_OK);

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(I18N_OPERATION_NOT_OK);

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
            ->formatResults(function (ResultSet $results) {
                return $results->map(function (Video $video) {
                    $video->created = $video->created->i18nFormat(FORMAT_FOR_MYSQL);

                    return $video;
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
                $this->Flash->success(I18N_OPERATION_OK);

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(I18N_OPERATION_NOT_OK);
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

        $this->Videos->deleteOrFail($this->Videos->get($id));

        $this->Flash->success(I18N_OPERATION_OK);

        return $this->redirect(['action' => 'index']);
    }
}
