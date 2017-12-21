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
namespace MeCmsYoutube\Controller;

use Cake\Event\Event;
use MeCms\Controller\AppController as MeCmsAppController;

/**
 * Application controller class
 */
class AppController extends MeCmsAppController
{
    /**
     * Called after the controller action is run, but before the view is
     *  rendered.
     * You can use this method to perform logic or set view variables that
     *  are required on every request.
     * @param \Cake\Event\Event $event An Event instance
     * @return void
     * @see http://api.cakephp.org/3.4/class-Cake.Controller.Controller.html#_beforeRender
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        //Uses a custom View class (`MeCmsYoutube.AppView` or `MeCms.AdminView`)
        if ($this->request->isAdmin()) {
            $className = ME_CMS . '.View/Admin';
        } else {
            $className = ME_CMS_YOUTUBE . '.View/App';
        }

        $this->viewBuilder()->setClassName($className);
    }
}
