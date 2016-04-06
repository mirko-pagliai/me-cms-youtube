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
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @see         MeCms\Utility\SitemapBuilder
 */
namespace MeYoutube\Utility;

use Cake\ORM\TableRegistry;
use MeCms\Utility\SitemapBuilder;

/**
 * This class contains methods called by the `SitemapBuilder`.
 * Each method must be return an array or urls to add to the sitemap.
 */
class Sitemap extends SitemapBuilder {
    /**
     * Method that returns videos urls
     * @return array
     * @uses MeCms\Utility\SitemapBuilder::url()
     */
    public static function videos() {
        $categories = TableRegistry::get('MeYoutube.VideosCategories')->find('active')
            ->select(['id', 'slug'])
            ->contain(['Videos' => function($q) {
                return $q->select(['id', 'category_id']);
            }]);
        
        if($categories->isEmpty()) {
            return [];
        }
        
        //Adds videos index, categories index and videos search
        $url = [
            self::url(['_name' => 'videos']),
            self::url(['_name' => 'videos_categories']),
            self::url(['_name' => 'search_videos']),
        ];
        
        foreach($categories as $category) {
            //Adds the category
            $url[] = self::url(['_name' => 'videos_category', $category->slug]);
            
            //Adds the videos
            $url = am($url, array_map(function($video) {
                return self::url(['_name' => 'video', $video->id]);
            }, $category->videos));
        }
            
        return $url;
    }
}