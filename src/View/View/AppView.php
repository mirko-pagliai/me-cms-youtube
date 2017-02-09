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

use MeCms\View\View\AppView as View;

/**
 * Application view class
 */
class AppView extends View
{
    /**
     * Renders a layout. Returns output from _render(). Returns false on
     *  error. Several variables are created for use in layout
     * @param string $content Content to render in a view, wrapped by the
     *  surrounding layout
     * @param string|null $layout Layout name
     * @return mixed Rendered output, or false on error
     * @see http://api.cakephp.org/3.3/class-Cake.View.View.html#_renderLayout
     * @throws Cake\Core\Exception\Exception
     * @uses MeCms\View\View\AppView::renderLayout()
     * @uses MeTools\View\Helper\HtmlHelper::meta()
     */
    public function renderLayout($content, $layout = null)
    {
        //Automatically adds the meta tag for RSS videos
        if (config('default.rss_meta')) {
            $this->Html->meta(__d('me_cms_youtube', 'Latest videos'), '/videos/rss', ['type' => 'rss']);
        }

        return parent::renderLayout($content, $layout);
    }
}
