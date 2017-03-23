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
namespace MeCmsYoutube\Model\Table;

use ArrayObject;
use Cake\Cache\Cache;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use MeCmsYoutube\Utility\Youtube;
use MeCms\Model\Table\AppTable;
use MeCms\Model\Table\Traits\IsOwnedByTrait;
use MeCms\Model\Table\Traits\NextToBePublishedTrait;

/**
 * Videos model
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Categories
 */
class VideosTable extends AppTable
{
    use IsOwnedByTrait;
    use NextToBePublishedTrait;

    /**
     * Name of the configuration to use for this table
     * @var string
     */
    public $cache = 'videos';

    /**
     * Internal method to get information about a video
     * @param string $id Video ID
     * @return mixed Object or `false`
     * @uses MeCmsYoutube\Utility\Youtube::getInfo()
     */
    protected function _getInfo($id)
    {
        return (new Youtube)->getInfo($id);
    }

    /**
     * Called after an entity has been deleted
     * @param \Cake\Event\Event $event Event object
     * @param \Cake\ORM\Entity $entity Entity object
     * @param \ArrayObject $options Options
     * @return void
     * @uses MeCms\Model\Table\AppTable::afterDelete()
     * @uses MeCms\Model\Table\Traits\NextToBePublishedTrait::setNextToBePublished()
     */
    public function afterDelete(Event $event, Entity $entity, ArrayObject $options)
    {
        parent::afterDelete($event, $entity, $options);

        //Sets the next record to be published
        $this->setNextToBePublished();
    }

    /**
     * Called after an entity is saved.
     * @param \Cake\Event\Event $event Event object
     * @param \Cake\ORM\Entity $entity Entity object
     * @param \ArrayObject $options Options
     * @return void
     * @uses MeCms\Model\Table\AppTable::afterSave()
     * @uses MeCms\Model\Table\Traits\NextToBePublishedTrait::setNextToBePublished()
     */
    public function afterSave(Event $event, Entity $entity, ArrayObject $options)
    {
        parent::afterSave($event, $entity, $options);

        //Sets the next record to be published
        $this->setNextToBePublished();
    }

    /**
     * Called before each entity is saved.
     * Stopping this event will abort the save operation.
     * @param \Cake\Event\Event $event Event
     * @param \Cake\Datasource\EntityInterface $entity Entity
     * @param \ArrayObject $options Options
     * @return bool
     * @uses _getInfo()
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        if ((empty($entity->seconds) || empty($entity->duration)) && !empty($entity->youtube_id)) {
            $info = $this->_getInfo($entity->youtube_id);

            if (empty($entity->seconds)) {
                $entity->seconds = $info->seconds;
            }

            if (empty($entity->duration)) {
                $entity->duration = $info->duration;
            }
        }

        return true;
    }

    /**
     * Returns a rules checker object that will be used for validating application integrity
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users', __d('me_cms', 'You have to select a valid option')));
        $rules->add($rules->existsIn(['category_id'], 'Categories', __d('me_cms', 'You have to select a valid option')));

        return $rules;
    }

    /**
     * Creates a new Query for this repository and applies some defaults based on the type of search that was selected
     * @param string $type The type of query to perform
     * @param array|ArrayAccess $options An array that will be passed to Query::applyOptions()
     * @return Cake\ORM\Query The query builder
     * @uses MeCms\Model\Table\Traits\NextToBePublishedTrait::getNextToBePublished()
     * @uses MeCms\Model\Table\Traits\NextToBePublishedTrait::setNextToBePublished()
     */
    public function find($type = 'all', $options = [])
    {
        //Gets from cache the timestamp of the next record to be published
        $next = Cache::read('next_to_be_published', $this->cache);

        //If the cache is not valid, it empties the cache
        if ($next && time() >= $next) {
            Cache::clear(false, $this->cache);

            //Sets the next record to be published
            $this->setNextToBePublished();
        }

        return parent::find($type, $options);
    }

    /**
     * "Active" find method
     * @param Query $query Query object
     * @param array $options Options
     * @return Query Query object
     */
    public function findActive(Query $query, array $options)
    {
        $query->where([
            sprintf('%s.active', $this->getAlias()) => true,
            sprintf('%s.is_spot', $this->getAlias()) => false,
            sprintf('%s.created <=', $this->getAlias()) => new Time,
        ]);

        return $query;
    }

    /**
     * Gets random spots
     * @param int $limit Limit
     * @return array Spots
     * @uses $cache
     */
    public function getRandomSpots($limit = 1)
    {
        return $this->find()
            ->select('youtube_id')
            ->where([
                sprintf('%s.active', $this->getAlias()) => true,
                sprintf('%s.is_spot', $this->getAlias()) => true,
                sprintf('%s.created <=', $this->getAlias()) => new Time,
            ])
            ->cache('all_spots', $this->cache)
            ->sample($limit)
            ->toArray();
    }

    /**
     * Initialize method
     * @param array $config The configuration for the table
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('youtube_videos');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('Categories', ['className' => 'MeCmsYoutube.VideosCategories'])
            ->setForeignKey('category_id')
            ->setJoinType('INNER');

        $this->belongsTo('Users', ['className' => 'MeCms.Users'])
            ->setForeignKey('user_id')
            ->setJoinType('INNER');

        $this->addBehavior('Timestamp');
        $this->addBehavior('CounterCache', ['Categories' => ['video_count']]);

        $this->_validatorClass = '\MeCmsYoutube\Model\Validation\VideoValidator';
    }

    /**
     * Build query from filter data
     * @param Query $query Query object
     * @param array $data Filter data ($this->request->getQuery())
     * @return Query $query Query object
     * @uses \MeCms\Model\Table\AppTable::queryFromFilter()
     */
    public function queryFromFilter(Query $query, array $data = [])
    {
        $query = parent::queryFromFilter($query, $data);

        //"Is spot?" field
        if (!empty($data['spot']) && $data['spot']) {
            $query->where([sprintf('%s.is_spot', $this->getAlias()) => true]);
        }

        return $query;
    }
}
