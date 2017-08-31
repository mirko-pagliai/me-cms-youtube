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
 * @see         MeCms\Utility\SitemapBuilder
 */
namespace MeCmsYoutube\Utility;

use Cake\Cache\Cache;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use MeCms\Utility\SitemapBuilder;

/**
 * This class contains methods called by the `SitemapBuilder`.
 * Each method must be return an array or urls to add to the sitemap.
 *
 * This helper contains methods that will be called automatically to generate
 * the menu of the admin layout.
 * You do not need to call these methods manually.
 */
class Sitemap extends SitemapBuilder
{
    /**
     * Returns videos urls
     * @return array
     * @uses MeCms\Utility\SitemapBuilder::parse()
     */
    public static function videos()
    {
        $table = TableRegistry::get('MeCmsYoutube.VideosCategories');

        $url = Cache::read('sitemap', $table->cache);

        if ($url) {
            return $url;
        }

        $categories = $table->find('active')
            ->select(['id', 'lft', 'slug'])
            ->contain($table->Videos->getAlias(), function (Query $q) use ($table) {
                return $q->find('active')
                    ->select(['id', 'category_id', 'modified'])
                    ->order([sprintf('%s.modified', $table->Videos->getAlias()) => 'DESC']);
            })
            ->order(['lft' => 'ASC']);

        if ($categories->isEmpty()) {
            return [];
        }

        $latest = $table->Videos->find()
            ->select(['modified'])
            ->order([sprintf('%s.modified', $table->Videos->getAlias()) => 'DESC'])
            ->firstOrFail();

        //Adds videos index, categories index and videos search
        $url[] = self::parse(['_name' => 'videos'], ['lastmod' => $latest->modified]);
        $url[] = self::parse(['_name' => 'videosCategories']);
        $url[] = self::parse(['_name' => 'videosSearch'], ['priority' => '0.2']);

        foreach ($categories as $category) {
            //Adds the category
            $url[] = self::parse(
                ['_name' => 'videosCategory', $category->slug],
                ['lastmod' => $category->videos[0]->modified]
            );

            //Adds each video
            foreach ($category->videos as $video) {
                $url[] = self::parse(
                    ['_name' => 'video', $video->id],
                    ['lastmod' => $video->modified]
                );
            }
        }

        Cache::write('sitemap', $url, $table->cache);

        return $url;
    }
}
