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
namespace MeCmsYoutube\View\View;

use MeCms\View\View\AppView as MeCmsAppView;

/**
 * Application view class
 */
class AppView extends MeCmsAppView
{
    /**
     * Internal method to set some blocks
     * @return void
     * @uses MeTools\View\Helper\HtmlHelper::meta()
     */
    protected function _setBlocks()
    {
        parent::_setBlocks();

        //Sets the meta tag for RSS posts
        if (config('default.rss_meta')) {
            $this->Html->meta(__d('me_cms_youtube', 'Latest videos'), '/videos/rss', ['type' => 'rss']);
        }
    }
}
