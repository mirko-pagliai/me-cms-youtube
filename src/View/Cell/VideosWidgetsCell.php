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
namespace MeCmsYoutube\View\Cell;

use Cake\Event\EventManager;
use Cake\I18n\FrozenDate;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\View\Cell;

/**
 * VideosWidgets cell
 */
class VideosWidgetsCell extends Cell
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
        Request $request = null,
        Response $response = null,
        EventManager $eventManager = null,
        array $cellOptions = []
    ) {
        parent::__construct($request, $response, $eventManager, $cellOptions);

        $this->loadModel('MeCmsYoutube.Videos');
    }

    /**
     * Categories widget
     * @param string $render Render type (`form` or `list`)
     * @return void
     */
    public function categories($render = 'form')
    {
        $this->viewBuilder()->setTemplate(sprintf('categories_as_%s', $render));

        //Returns on categories index
        if ($this->request->isUrl(['_name' => 'videosCategories'])) {
            return;
        }

        $categories = $this->Videos->Categories->find('active')
            ->select(['title', 'slug', 'video_count'])
            ->order([sprintf('%s.title', $this->Videos->Categories->getAlias()) => 'ASC'])
            ->formatResults(function ($results) {
                return $results->indexBy('slug');
            })
            ->cache('widget_categories', $this->Videos->cache)
            ->toArray();

        $this->set(compact('categories'));
    }

    /**
     * Latest widget
     * @param int $limit Limit
     * @return void
     */
    public function latest($limit = 1)
    {
        //Returns on posts index
        if ($this->request->isUrl(['_name' => 'videos'])) {
            return;
        }

        $videos = $this->Videos->find('active')
            ->select(['id', 'youtube_id', 'title', 'text'])
            ->limit($limit)
            ->order([sprintf('%s.created', $this->Videos->getAlias()) => 'DESC'])
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
        $this->viewBuilder()->setTemplate(sprintf('months_as_%s', $render));

        //Returns on posts index
        if ($this->request->isUrl(['_name' => 'videos'])) {
            return;
        }
        $query = $this->Videos->find('active');
        $time = $query->func()->date_format(['created' => 'identifier', "'%Y/%m'" => 'literal']);
        $months = $query->select([
                'month' => $time,
                'video_count' => $query->func()->count($time),
            ])
            ->distinct(['month'])
            ->formatResults(function ($results) {
                return $results->indexBy('month')->map(function ($row) {
                    list($year, $month) = explode('/', $row->month);
                    $row->month = (new FrozenDate())->day(1)->month($month)->year($year);

                    return $row;
                });
            })
            ->order(['month' => 'DESC'])
            ->cache('widget_months', $this->Videos->cache)
            ->toArray();

        $this->set(compact('months'));
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
