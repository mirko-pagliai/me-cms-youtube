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

use Cake\Cache\Cache;
use Cake\Event\Event;
use Cake\Network\Exception\ForbiddenException;
use MeCmsYoutube\Controller\AppController;
use MeCms\Controller\Traits\CheckLastSearchTrait;
use MeCms\Controller\Traits\GetStartAndEndDateTrait;

/**
 * Videos controller
 * @property \MeCmsYoutube\Model\Table\VideosTable $Videos
 */
class VideosController extends AppController
{
    use CheckLastSearchTrait;
    use GetStartAndEndDateTrait;

    /**
     * Called before the controller action.
     * You can use this method to perform logic that needs to happen before
     *  each controller action.
     * @param \Cake\Event\Event $event An Event instance
     * @return void
     * @see http://api.cakephp.org/3.4/class-Cake.Controller.Controller.html#_beforeFilter
     * @uses MeCms\Controller\AppController::beforeFilter()
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->Auth->deny('preview');
    }

    /**
     * Lists videos
     * @return void
     */
    public function index()
    {
        $page = $this->request->getQuery('page', 1);

        //Sets the cache name
        $cache = sprintf('index_limit_%s_page_%s', $this->paginate['limit'], $page);

        //Tries to get data from the cache
        list($videos, $paging) = array_values(Cache::readMany(
            [$cache, sprintf('%s_paging', $cache)],
            $this->Videos->cache
        ));

        //If the data are not available from the cache
        if (empty($videos) || empty($paging)) {
            $query = $this->Videos->find('active')
                ->select(['id', 'youtube_id', 'title', 'subtitle', 'text', 'created'])
                ->contain([
                    'Categories' => ['fields' => ['title', 'slug']],
                    'Users' => ['fields' => ['first_name', 'last_name']],
                ])
                ->where(['is_spot' => false])
                ->order([sprintf('%s.created', $this->Videos->getAlias()) => 'DESC']);

            $videos = $this->paginate($query);

            //Writes on cache
            Cache::writeMany([
                $cache => $videos,
                sprintf('%s_paging', $cache) => $this->request->getParam('paging'),
            ], $this->Videos->cache);
        //Else, sets the paging parameter
        } else {
            $this->request = $this->request->withParam('paging', $paging);
        }

        $this->set(compact('videos'));
    }

    /**
     * Lists videos for a specific date.
     *
     * Month and day are optional and you can also use special keywords `today`
     *  or `yesterday`.
     *
     * Examples:
     * <pre>/videos/2016/06/11</pre>
     * <pre>/videos/2016/06</pre>
     * <pre>/videos/2016</pre>
     * <pre>/videos/today</pre>
     * <pre>/videos/yesterday</pre>
     * @param string $date Date as `today`, `yesterday`, `YYYY/MM/dd`,
     *  `YYYY/MM` or `YYYY`
     * @return \Cake\Network\Response|null|void
     * @use \MeCms\Controller\Traits\GetStartAndEndDateTrait\getStartAndEndDate()
     */
    public function indexByDate($date)
    {
        //Data can be passed as query string, from a widget
        if ($this->request->getQuery('q')) {
            return $this->redirect([$this->request->getQuery('q')]);
        }

        list($start, $end) = $this->getStartAndEndDate($date);

        $page = $this->request->getQuery('page', 1);

        //Sets the cache name
        $cache = sprintf('index_date_%s_limit_%s_page_%s', md5(serialize([$start, $end])), $this->paginate['limit'], $page);

        //Tries to get data from the cache
        list($videos, $paging) = array_values(Cache::readMany(
            [$cache, sprintf('%s_paging', $cache)],
            $this->Videos->cache
        ));

        //If the data are not available from the cache
        if (empty($videos) || empty($paging)) {
            $query = $this->Videos->find('active')
                ->select(['id', 'youtube_id', 'title', 'subtitle', 'text', 'created'])
                ->contain([
                    'Categories' => ['fields' => ['title', 'slug']],
                    'Users' => ['fields' => ['first_name', 'last_name']],
                ])
                ->order([sprintf('%s.created', $this->Videos->getAlias()) => 'DESC'])
                ->where([
                    'is_spot' => false,
                    sprintf('%s.created >=', $this->Videos->getAlias()) => $start,
                    sprintf('%s.created <', $this->Videos->getAlias()) => $end,
                ])
                ->order([sprintf('%s.created', $this->Videos->getAlias()) => 'DESC']);

            $videos = $this->paginate($query);

            //Writes on cache
            Cache::writeMany([
                $cache => $videos,
                sprintf('%s_paging', $cache) => $this->request->getParam('paging'),
            ], $this->Videos->cache);
        //Else, sets the paging parameter
        } else {
            $this->request = $this->request->withParam('paging', $paging);
        }

        $this->set(compact('date', 'start', 'videos'));
    }

    /**
     * Lists videos as RSS
     * @return void
     * @throws \Cake\Network\Exception\ForbiddenException
     */
    public function rss()
    {
        //This method works only for RSS
        if (!$this->RequestHandler->isRss()) {
            throw new ForbiddenException();
        }

        $videos = $this->Videos->find('active')
            ->select(['id', 'youtube_id', 'title', 'text', 'created'])
            ->where(['is_spot' => false])
            ->limit(config('default.records_for_rss'))
            ->order([sprintf('%s.created', $this->Videos->getAlias()) => 'DESC'])
            ->cache('rss', $this->Videos->cache);

        $this->set(compact('videos'));

        $this->viewBuilder()->setLayout('MeCms.default');
    }

    /**
     * Searches videos
     * @return Cake\Network\Response|null
     * @uses MeCms\Controller\Traits\CheckLastSearchTrait::checkLastSearch()
     */
    public function search()
    {
        $pattern = $this->request->getQuery('p');

        //Checks if the pattern is at least 4 characters long
        if ($pattern && strlen($pattern) < 4) {
            $this->Flash->alert(__d('me_cms', 'You have to search at least a word of {0} characters', 4));

            return $this->redirect([]);
        }

        //Checks the last search
        if ($pattern && !$this->checkLastSearch($pattern)) {
            $this->Flash->alert(__d(
                'me_cms',
                'You have to wait {0} seconds to perform a new search',
                config('security.search_interval')
            ));

            return $this->redirect([]);
        }

        if ($pattern) {
            $this->paginate['limit'] = config('default.records_for_searches');

            $page = $this->request->getQuery('page', 1);

            //Sets the cache name
            $cache = sprintf('search_%s_limit_%s_page_%s', md5($pattern), $this->paginate['limit'], $page);

            //Tries to get data from the cache
            list($videos, $paging) = array_values(Cache::readMany(
                [$cache, sprintf('%s_paging', $cache)],
                $this->Videos->cache
            ));

            //If the data are not available from the cache
            if (empty($videos) || empty($paging)) {
                $query = $this->Videos->find('active')
                    ->select(['id', 'title', 'text', 'created'])
                    ->where(['OR' => [
                        'title LIKE' => sprintf('%%%s%%', $pattern),
                        'subtitle LIKE' => sprintf('%%%s%%', $pattern),
                        'text LIKE' => sprintf('%%%s%%', $pattern),
                    ]])
                    ->order([sprintf('%s.created', $this->Videos->getAlias()) => 'DESC']);

                $videos = $this->paginate($query);

                //Writes on cache
                Cache::writeMany([
                    $cache => $videos,
                    sprintf('%s_paging', $cache) => $this->request->getParam('paging')
                ], $this->Videos->cache);
            //Else, sets the paging parameter
            } else {
                $this->request = $this->request->withParam('paging', $paging);
            }

            $this->set(compact('videos'));
        }

        $this->set(compact('pattern'));
    }

    /**
     * Views video
     * @param string $id Video ID
     * @return void
     * @uses MeCmsYoutube\Model\Table\VideosTable::getRandomSpots()
     */
    public function view($id = null)
    {
        $video = $this->Videos->find('active')
            ->select(['id', 'youtube_id', 'title', 'subtitle', 'text', 'active', 'is_spot', 'created', 'modified'])
            ->contain([
                'Categories' => ['fields' => ['title', 'slug']],
                'Users' => ['fields' => ['first_name', 'last_name']],
            ])
            ->where([sprintf('%s.id', $this->Videos->getAlias()) => $id])
            ->cache(sprintf('view_%s', md5($id)), $this->Videos->cache)
            ->firstOrFail();

        //If requested, gets the ID of a spot and adds it to the video
        if (!$video->is_spot && config('video.spot')) {
            $spot = $this->Videos->getRandomSpots()[0]->youtube_id;
            $this->set(compact('spot'));
        }

        $this->set(compact('video'));
    }

    /**
     * Preview for videos.
     * It uses the `view` template.
     * @param string $id Video ID
     * @return void
     * @uses MeCmsYoutube\Model\Table\VideosTable::getRandomSpots()
     */
    public function preview($id = null)
    {
        $video = $this->Videos->find()
            ->select(['id', 'youtube_id', 'title', 'subtitle', 'text', 'active', 'is_spot', 'created', 'modified'])
            ->contain([
                'Categories' => ['fields' => ['title', 'slug']],
                'Users' => ['fields' => ['first_name', 'last_name']],
            ])
            ->where([sprintf('%s.id', $this->Videos->getAlias()) => $id])
            ->firstOrFail();

        //If requested, gets the ID of a spot and adds it to the video
        if (!$video->is_spot && config('video.spot')) {
            $spot = $this->Videos->getRandomSpots()[0]->youtube_id;
            $this->set(compact('spot'));
        }

        $this->set(compact('video'));

        $this->render('view');
    }
}
