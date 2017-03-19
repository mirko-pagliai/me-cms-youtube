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
use Cake\I18n\Time;
use Cake\Network\Exception\ForbiddenException;
use MeCmsYoutube\Controller\AppController;
use MeCms\Controller\Traits\CheckLastSearchTrait;

/**
 * Videos controller
 * @property \MeCmsYoutube\Model\Table\VideosTable $Videos
 */
class VideosController extends AppController
{
    use CheckLastSearchTrait;

    /**
     * Called before the controller action.
     * You can use this method to perform logic that needs to happen before
     *  each controller action.
     * @param \Cake\Event\Event $event An Event instance
     * @return void
     * @see http://api.cakephp.org/3.4/class-Cake.Controller.Controller.html#_beforeFilter
     * @uses MeCms\Controller\AppController::beforeFilter()
     */
    public function beforeFilter(\Cake\Event\Event $event)
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
        $page = $this->request->getQuery('page') ?: 1;

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
                    'Categories' => function ($q) {
                        return $q->select(['title', 'slug']);
                    },
                    'Users' => function ($q) {
                        return $q->select(['first_name', 'last_name']);
                    },
                ])
                ->where(['is_spot' => false])
                ->order([sprintf('%s.created', $this->Videos->alias()) => 'DESC']);

            $videos = $this->paginate($query)->toArray();

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
     * List videos for a specific date:
     *
     * The date must be passed in the format:
     * <pre>YYYY/MM/dd</pre>
     * The month and day are optional.
     * You can also use the special keywords "today" and "yesterday".
     *
     * Examples:
     * <pre>/videos/2016/06/11</pre>
     * <pre>/videos/2016/06</pre>
     * <pre>/videos/2016</pre>
     * <pre>/videos/today</pre>
     * <pre>/videos/yesterday</pre>
     * @param string $date Date as `YYYY/MM/dd`
     * @return \Cake\Network\Response|null|void
     */
    public function indexByDate($date = null)
    {
        //Data can be passed as query string, from a widget
        if ($this->request->getQuery('q')) {
            return $this->redirect([$this->request->getQuery('q')]);
        }

        //Sets `$year`, `$month` and `$day`
        //`$month` and `$day` may be `null`
        if ($date === 'today' || $date === 'yesterday') {
            $date = new Time($date === 'today' ? 'now' : '1 days ago');

            list($year, $month, $day) = explode('/', $date->i18nFormat('YYYY/MM/dd'));
        } else {
            list($year, $month, $day) = am(explode('/', $date), [null, null, null]);
        }

        //Sets the start date
        $start = (new Time())
            ->setDate($year, empty($month) ? 1 : $month, empty($day) ? 1 : $day)
            ->setTime(0, 0, 0);

        //Sets the end date
        if ($year && $month && $day) {
            $end = (new Time($start))->addDay(1);
        } elseif ($year && $month) {
            $end = (new Time($start))->addMonth(1);
        } else {
            $end = (new Time($start))->addYear(1);
        }

        $page = $this->request->getQuery('page') ?: 1;

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
                    'Categories' => function ($q) {
                        return $q->select(['title', 'slug']);
                    },
                    'Users' => function ($q) {
                        return $q->select(['first_name', 'last_name']);
                    },
                ])
                ->order([sprintf('%s.created', $this->Videos->alias()) => 'DESC'])
                ->where([
                    'is_spot' => false,
                    sprintf('%s.created >=', $this->Videos->alias()) => $start,
                    sprintf('%s.created <', $this->Videos->alias()) => $end,
                ])
                ->order([sprintf('%s.created', $this->Videos->alias()) => 'DESC']);

            $videos = $this->paginate($query)->toArray();

            //Writes on cache
            Cache::writeMany([
                $cache => $videos,
                sprintf('%s_paging', $cache) => $this->request->getParam('paging'),
            ], $this->Videos->cache);
        //Else, sets the paging parameter
        } else {
            $this->request = $this->request->withParam('paging', $paging);
        }

        $this->set(compact('videos', 'year', 'month', 'day'));
    }

    /**
     * This allows backward compatibility for URLs like:
     * <pre>/videos/page:3</pre>
     * <pre>/videos/page:3/sort:Video.created/direction:desc</pre>
     * These URLs will become:
     * <pre>/videos?page=3</pre>
     * @param int $page Page number
     * @return \Cake\Network\Response|null
     */
    public function indexCompatibility($page)
    {
        return $this->redirect(['_name' => 'videos', '?' => ['page' => $page]], 301);
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
            ->order([sprintf('%s.created', $this->Videos->alias()) => 'DESC'])
            ->cache('rss', $this->Videos->cache);

        $this->set(compact('videos'));

        $this->viewBuilder()->layout('MeCms.default');
    }

    /**
     * Searches videos
     * @return void
     * @uses MeCms\Controller\Traits\CheckLastSearchTrait::checkLastSearch()
     */
    public function search()
    {
        $pattern = $this->request->getQuery('p');

        if ($pattern) {
            //Checks if the pattern is at least 4 characters long
            if (strlen($pattern) >= 4) {
                if ($this->checkLastSearch($pattern)) {
                    $this->paginate['limit'] = config('default.records_for_searches');

                    $page = $this->request->getQuery('page') ?: 1;

                    //Sets the initial cache name
                    $cache = sprintf('search_%s', md5($pattern));

                    //Updates the cache name with the query limit and the number of the page
                    $cache = sprintf('%s_limit_%s', $cache, $this->paginate['limit']);
                    $cache = sprintf('%s_page_%s', $cache, $page);

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
                            ->order([sprintf('%s.created', $this->Videos->alias()) => 'DESC']);

                        $videos = $this->paginate($query)->toArray();

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
                } else {
                    $this->Flash->alert(__d('me_cms', 'You have to wait {0} seconds to perform a new search', config('security.search_interval')));
                }
            } else {
                $this->Flash->alert(__d('me_cms', 'You have to search at least a word of {0} characters', 4));
            }
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
                'Categories' => function ($q) {
                    return $q->select(['title', 'slug']);
                },
                'Users' => function ($q) {
                    return $q->select(['first_name', 'last_name']);
                },
            ])
            ->where([sprintf('%s.id', $this->Videos->alias()) => $id])
            ->cache(sprintf('view_%s', md5($id)), $this->Videos->cache)
            ->firstOrFail();

        //If requested, gets the ID of a spot and adds it to the video
        if (!$video->is_spot && config('video.spot')) {
            $spot = $this->Videos->getRandomSpots();
            $video->spot_id = $spot[0]->youtube_id;
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
                'Categories' => function ($q) {
                    return $q->select(['title', 'slug']);
                },
                'Users' => function ($q) {
                    return $q->select(['first_name', 'last_name']);
                },
            ])
            ->where([sprintf('%s.id', $this->Videos->alias()) => $id])
            ->firstOrFail();

        //If requested, gets the ID of a spot and adds it to the video
        if (!$video->is_spot && config('video.spot')) {
            $spot = $this->Videos->getRandomSpots();
            $video->spot_id = $spot[0]->youtube_id;
        }

        $this->set(compact('video'));

        $this->render('view');
    }
}
