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
 * @see         MeCms\View\Helper\MenuBuilderHelper
 */
namespace MeCmsYoutube\View\Helper;

use Cake\View\Helper;

/**
 * Menu Helper.
 *
 * This helper contains methods that will be called automatically to generate
 * the menu of the admin layout.
 * You do not need to call these methods manually.
 */
class MenuHelper extends Helper
{
    /**
     * Helpers
     * @var array
     */
    public $helpers = [
        'Html' => ['className' => 'MeTools.Html'],
        'MeCms.Auth',
    ];

    /**
     * Internal function to generate the menu for "videos" actions
     * @return mixed Array with menu, title and link options
     * @uses MeCms\View\Helper\AuthHelper::isGroup()
     * @uses MeTools\View\Helper\HtmlHelper::link()
     */
    public function videos()
    {
        $menu[] = $this->Html->link(__d('me_cms_youtube', 'List videos'), [
            'controller' => 'Videos',
            'action' => 'index',
            'plugin' => ME_CMS_YOUTUBE,
            'prefix' => ADMIN_PREFIX,
        ]);
        $menu[] = $this->Html->link(__d('me_cms_youtube', 'Add video'), [
            'controller' => 'Videos',
            'action' => 'add',
            'plugin' => ME_CMS_YOUTUBE,
            'prefix' => ADMIN_PREFIX,
        ]);

        //Only admins and managers can access this controller
        if ($this->Auth->isGroup(['admin', 'manager'])) {
            $menu[] = $this->Html->link(__d('me_cms', 'List categories'), [
                'controller' => 'VideosCategories',
                'action' => 'index',
                'plugin' => ME_CMS_YOUTUBE,
                'prefix' => ADMIN_PREFIX,
            ]);
            $menu[] = $this->Html->link(__d('me_cms', 'Add category'), [
                'controller' => 'VideosCategories',
                'action' => 'add',
                'plugin' => ME_CMS_YOUTUBE,
                'prefix' => ADMIN_PREFIX,
            ]);
        }

        return [$menu, __d('me_cms_youtube', 'Videos'), ['icon' => 'film']];
    }
}
