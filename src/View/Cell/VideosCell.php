<?php
/**
 * This file is part of MeYoutube.
 *
 * MeYoutube is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeYoutube is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeYoutube.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeYoutube\View\Cell;

use Cake\Cache\Cache;
use Cake\I18n\FrozenDate;
use Cake\View\Cell;

/**
 * Videos cell
 */
class VideosCell extends Cell
{
    /**
     * Constructor. It loads the model
     * @param \Cake\Network\Request $request The request to use in the cell
     * @param \Cake\Network\Response $response The request to use in the cell
     * @param \Cake\Event\EventManager $eventManager The eventManager to bind events to
     * @param array $cellOptions Cell options to apply
     * @uses Cake\View\Cell::__construct()
     */
    public function __construct(
        \Cake\Network\Request $request = null,
        \Cake\Network\Response $response = null,
        \Cake\Event\EventManager $eventManager = null,
        array $cellOptions = []
    ) {
        parent::__construct($request, $response, $eventManager, $cellOptions);

        //Loads the Videos model
        $this->loadModel('MeYoutube.Videos');
    }

    /**
     * Categories widget
     * @param string $render Render type (`form` or `list`)
     * @return void
     */
    public function categories($render = 'form')
    {
        //Returns on categories index
        if ($this->request->is('here', ['_name' => 'videosCategories'])) {
            return;
        }

        $categories = $this->Videos->Categories->find('active')
            ->select(['title', 'slug', 'video_count'])
            ->order(['title' => 'ASC'])
            ->cache('widget_categories', $this->Videos->cache)
            ->toArray();

        foreach ($categories as $k => $category) {
            $categories[$category->slug] = $category;
            unset($categories[$k]);
        }

        $this->set(compact('categories'));

        if ($render !== 'form') {
            $this->viewBuilder()->template(sprintf('categories_as_%s', $render));
        }
    }

    /**
     * Latest widget
     * @param int $limit Limit
     * @return void
     */
    public function latest($limit = 1)
    {
        //Returns on index, except for category
        if ($this->request->is('action', 'index', 'Videos') && !$this->request->param('slug')) {
            return;
        }

        $videos = $this->Videos->find('active')
            ->select(['id', 'youtube_id', 'title', 'text'])
            ->limit($limit)
            ->order(['created' => 'DESC'])
            ->cache(sprintf('widget_latest_%d', $limit), $this->Videos->cache)
            ->toArray();

        $this->set(compact('videos'));
    }

    /**
     * Videos by month widget
     * @param string $render Render type (`form` or `list`)
     * @return void
     */
    public function months($render = 'form')
    {
        //Returns on index
        if ($this->request->is('action', 'index', 'Videos')) {
            return;
        }

        $months = $this->Videos->find('active')
            ->select([
                'month' => 'DATE_FORMAT(created, "%m-%Y")',
                'video_count' => 'COUNT(DATE_FORMAT(created, "%m-%Y"))',
            ])
            ->distinct(['month'])
            ->order(['created' => 'DESC'])
            ->cache('widget_months', $this->Videos->cache)
            ->toArray();

        foreach ($months as $oldKey => $month) {
            $exploded = explode('-', $month->month);
            $newKey = sprintf('%s/%s', $exploded[1], $exploded[0]);

            $months[$newKey] = $month;
            $months[$newKey]->month = (new FrozenDate())->year($exploded[1])->month($exploded[0]);
            unset($months[$oldKey]);
        }

        $this->set(compact('months'));

        if ($render !== 'form') {
            $this->viewBuilder()->template(sprintf('months_as_%s', $render));
        }
    }

    /**
     * Random widget
     * @param int $limit Limit
     * @return void
     */
    public function random($limit = 1)
    {
        //Returns on the same controllers
        if ($this->request->is('controller', ['Videos', 'VideosCategories'])) {
            return;
        }

        //Returns, if there are no records available
        if (Cache::read($cache = 'no_videos', $this->Videos->cache)) {
            return;
        }

        //Gets videos
        $videos = $this->Videos->find('active')
            ->select(['id', 'youtube_id', 'title', 'text'])
            ->limit($limit)
            ->order('rand()')
            ->toArray();

        //Writes on cache, if there are no records available
        if (empty($videos)) {
            Cache::write($cache, true, $this->Videos->cache);
        }

        $this->set(compact('videos'));
    }

    /**
     * Search widget
     * @return void
     */
    public function search()
    {
        //For this widget, control of the action takes place in the view
    }
}
