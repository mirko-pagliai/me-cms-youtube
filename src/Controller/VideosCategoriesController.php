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

use Cake\Cache\Cache;
use Cake\Datasource\Exception\RecordNotFoundException;
use MeCmsYoutube\Controller\AppController;

/**
 * VideosCategories controller
 * @property \MeCmsYoutube\Model\Table\VideosCategoriesTable $VideosCategories
 */
class VideosCategoriesController extends AppController
{
    /**
     * Lists videos categories
     * @return void
     */
    public function index()
    {
        $categories = $this->VideosCategories->find('active')
            ->select(['title', 'slug'])
            ->order([sprintf('%s.title', $this->VideosCategories->getAlias()) => 'ASC'])
            ->cache('categories_index', $this->VideosCategories->cache);

        $this->set(compact('categories'));
    }

    /**
     * Lists videos for a category
     * @param string $slug Category slug
     * @return \Cake\Network\Response|null|void
     */
    public function view($slug = null)
    {
        //The category can be passed as query string, from a widget
        if ($this->request->getQuery('q')) {
            return $this->redirect([$this->request->getQuery('q')]);
        }

        $page = $this->request->getQuery('page', 1);

        //Sets the cache name
        $cache = sprintf('category_%s_limit_%s_page_%s', md5($slug), $this->paginate['limit'], $page);

        //Tries to get data from the cache
        list($videos, $paging) = array_values(Cache::readMany(
            [$cache, sprintf('%s_paging', $cache)],
            $this->VideosCategories->cache
        ));

        //If the data are not available from the cache
        if (empty($videos) || empty($paging)) {
            $query = $this->VideosCategories->Videos->find('active')
                ->select(['id', 'youtube_id', 'title', 'subtitle', 'text', 'created'])
                ->contain([
                    'Categories' => ['fields' => ['id', 'title', 'slug']],
                    'Users' => ['fields' => ['first_name', 'last_name']],
                ])
                ->where([
                    sprintf('%s.slug', $this->VideosCategories->getAlias()) => $slug,
                    'is_spot' => false,
                ])
                ->order([sprintf('%s.created', $this->VideosCategories->Videos->getAlias()) => 'DESC']);

            if ($query->isEmpty()) {
                throw new RecordNotFoundException(I18N_NOT_FOUND);
            }

            $videos = $this->paginate($query);

            //Writes on cache
            Cache::writeMany([
                $cache => $videos,
                sprintf('%s_paging', $cache) => $this->request->getParam('paging'),
            ], $this->VideosCategories->cache);
        //Else, sets the paging parameter
        } else {
            $this->request = $this->request->withParam('paging', $paging);
        }

        $this->set('category', $videos->extract('category')->first());
        $this->set(compact('videos'));
    }
}
