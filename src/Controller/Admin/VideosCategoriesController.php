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
use MeCmsYoutube\Controller\AppController;

/**
 * VideosCategories controller
 * @property \MeCmsYoutube\Model\Table\VideosCategoriesTable $VideosCategories
 */
class VideosCategoriesController extends AppController
{
    /**
     * Called before the controller action.
     * You can use this method to perform logic that needs to happen before
     *  each controller action.
     * @param \Cake\Event\Event $event An Event instance
     * @return void
     * @uses MeCms\Controller\AppController::beforeFilter()
     * @uses MeCmsYoutube\Model\Table\VideosCategoriesTable::getTreeList()
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        if ($this->request->isAction(['add', 'edit'])) {
            $this->set('categories', $this->VideosCategories->getTreeList());
        }
    }

    /**
     * Checks if the provided user is authorized for the request
     * @param array $user The user to check the authorization of. If empty the
     *  user in the session will be used
     * @return bool `true` if the user is authorized, otherwise `false`
     * @uses MeCms\Controller\AppController::isAuthorized()
     * @uses MeCms\Controller\Component\AuthComponent::isGroup()
     */
    public function isAuthorized($user = null)
    {
        //Only admins can delete videos categories
        if ($this->request->isDelete()) {
            return $this->Auth->isGroup('admin');
        }

        //Admins and managers can access other actions
        return parent::isAuthorized($user);
    }

    /**
     * Lists videos categories
     * @return void
     * @uses MeCms\Model\Table\VideosCategoriesTable::getTreeList()
     */
    public function index()
    {
        $categories = $this->VideosCategories->find()
            ->contain(['Parents' => ['fields' => ['title']]])
            ->order([sprintf('%s.lft', $this->VideosCategories->alias()) => 'ASC'])
            ->formatResults(function ($categories) {
                //Gets categories as tree list
                $treeList = $this->VideosCategories->getTreeList()->toArray();

                return $categories->map(function ($category) use ($treeList) {
                    $category->title = $treeList[$category->id];

                    return $category;
                });
            });

        $this->set(compact('categories'));
    }

    /**
     * Adds videos category
     * @return \Cake\Network\Response|null|void
     */
    public function add()
    {
        $category = $this->VideosCategories->newEntity();

        if ($this->request->is('post')) {
            $category = $this->VideosCategories->patchEntity($category, $this->request->getData());

            if ($this->VideosCategories->save($category)) {
                $this->Flash->success(__d('me_cms', 'The operation has been performed correctly'));

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__d('me_cms', 'The operation has not been performed correctly'));
        }

        $this->set(compact('category'));
    }

    /**
     * Edits youtube videos category
     * @param string $id Videos category ID
     * @return \Cake\Network\Response|null|void
     */
    public function edit($id = null)
    {
        $category = $this->VideosCategories->get($id);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $category = $this->VideosCategories->patchEntity($category, $this->request->getData());

            if ($this->VideosCategories->save($category)) {
                $this->Flash->success(__d('me_cms', 'The operation has been performed correctly'));

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__d('me_cms', 'The operation has not been performed correctly'));
        }

        $this->set(compact('category'));
    }

    /**
     * Deletes videos category
     * @param string $id Videos category ID
     * @return \Cake\Network\Response|null
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $category = $this->VideosCategories->get($id);

        //Before deleting, it checks if the category has some videos
        if (!$category->video_count) {
            $this->VideosCategories->deleteOrFail($category);

            $this->Flash->success(__d('me_cms', 'The operation has been performed correctly'));
        } else {
            $this->Flash->alert(__d('me_cms', 'Before deleting this, you must delete or reassign all items that belong to this element'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
