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
namespace MeCmsYoutube\View\Cell;

use Cake\Event\EventManager;
use Cake\I18n\FrozenDate;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\ORM\ResultSet;
use Cake\View\Cell;
use MeCmsYoutube\Model\Entity\Video;

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
            ->formatResults(function (ResultSet $results) {
                return $results->indexBy('slug');
            })
            ->cache('widget_categories', $this->Videos->cache)
            ->all();

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
            ->all();

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
            ->formatResults(function (ResultSet $results) {
                return $results->indexBy('month')->map(function (Video $video) {
                    list($year, $month) = explode('/', $video->month);
                    $video->month = (new FrozenDate())->day(1)->month($month)->year($year);

                    return $video;
                });
            })
            ->order(['month' => 'DESC'])
            ->cache('widget_months', $this->Videos->cache)
            ->all();

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
