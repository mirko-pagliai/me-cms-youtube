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
            $className = 'MeCms.View/Admin';
        } else {
            $className = 'MeCmsYoutube.View/App';
        }

        $this->viewBuilder()->setClassName($className);
    }
}
