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
namespace MeYoutube\Model\Table;

use Cake\Cache\Cache;
use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;
use MeCms\Model\Table\AppTable;
use MeYoutube\Model\Entity\Video;

/**
 * Videos model
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Categories
 */
class VideosTable extends AppTable
{
    /**
     * Name of the configuration to use for this table
     * @var string|array
     */
    public $cache = 'videos';

    /**
     * Called after an entity has been deleted
     * @param \Cake\Event\Event $event Event object
     * @param \Cake\ORM\Entity $entity Entity object
     * @param \ArrayObject $options Options
     * @return void
     * @uses MeCms\Model\Table\AppTable::afterDelete()
     * @uses setNextToBePublished()
     */
    public function afterDelete(\Cake\Event\Event $event, \Cake\ORM\Entity $entity, \ArrayObject $options)
    {
        parent::afterDelete($event, $entity, $options);

        //Sets the next video to be published
        $this->setNextToBePublished();
    }

    /**
     * Called after an entity is saved.
     * @param \Cake\Event\Event $event Event object
     * @param \Cake\ORM\Entity $entity Entity object
     * @param \ArrayObject $options Options
     * @return void
     * @uses MeCms\Model\Table\AppTable::afterSave()
     * @uses setNextToBePublished()
     */
    public function afterSave(\Cake\Event\Event $event, \Cake\ORM\Entity $entity, \ArrayObject $options)
    {
        parent::afterSave($event, $entity, $options);

        //Sets the next video to be published
        $this->setNextToBePublished();
    }

    /**
     * Returns a rules checker object that will be used for validating application integrity
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['category_id'], 'Categories'));

        return $rules;
    }

    /**
     * Creates a new Query for this repository and applies some defaults based on the type of search that was selected
     * @param string $type The type of query to perform
     * @param array|ArrayAccess $options An array that will be passed to Query::applyOptions()
     * @return Cake\ORM\Query The query builder
     * @uses setNextToBePublished()
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
            sprintf('%s.active', $this->alias()) => true,
            sprintf('%s.is_spot', $this->alias()) => false,
            sprintf('%s.created <=', $this->alias()) => new Time(),
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
        //Gets all spots
        $spots = $this->find()
            ->select('youtube_id')
            ->where([
                sprintf('%s.active', $this->alias()) => true,
                sprintf('%s.is_spot', $this->alias()) => true,
                sprintf('%s.created <=', $this->alias()) => new Time(),
            ])
            ->cache('all_spots', $this->cache)
            ->toArray();

        //Shuffles
        shuffle($spots);

        //If the records are less than the limit, it returns all records
        return array_slice($spots, 0, count($spots) < $limit ? count($spots) : $limit);
    }

    /**
     * Initialize method
     * @param array $config The configuration for the table
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('youtube_videos');
        $this->displayField('title');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Categories', [
            'foreignKey' => 'category_id',
            'joinType' => 'INNER',
            'className' => 'MeYoutube.VideosCategories',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'className' => 'MeCms.Users',
        ]);

        $this->addBehavior('Timestamp');
        $this->addBehavior('CounterCache', ['Categories' => ['video_count']]);
    }

    /**
     * Build query from filter data
     * @param Query $query Query object
     * @param array $data Filter data ($this->request->query)
     * @return Query $query Query object
     * @uses \MeCms\Model\Table\AppTable::queryFromFilter()
     */
    public function queryFromFilter(Query $query, array $data = [])
    {
        $query = parent::queryFromFilter($query, $data);

        //"Is spot?" field
        if (!empty($data['spot']) && $data['spot']) {
            $query->where([
                sprintf('%s.is_spot', $this->alias()) => true,
            ]);
        }

        return $query;
    }

    /**
     * Sets to cache the timestamp of the next record to be published.
     * This value can be used to check if the cache is valid
     * @return void
     * @uses $cache
     */
    public function setNextToBePublished()
    {
        $next = $this->find()
            ->select('created')
            ->where([
                sprintf('%s.active', $this->alias()) => true,
                sprintf('%s.created >', $this->alias()) => new Time(),
            ])
            ->order([sprintf('%s.created', $this->alias()) => 'ASC'])
            ->first();

        $next = empty($next->created) ? false : $next->created->toUnixString();

        Cache::write('next_to_be_published', $next, $this->cache);
    }

    /**
     * Default validation rules
     * @param \Cake\Validation\Validator $validator Validator instance
     * @return \MeYoutube\Model\Validation\VideoValidator
     */
    public function validationDefault(\Cake\Validation\Validator $validator)
    {
        return new \MeYoutube\Model\Validation\VideoValidator;
    }
}
