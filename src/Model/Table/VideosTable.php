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
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
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
 */
class VideosTable extends AppTable {
	/**
	 * Called after an entity has been deleted
	 * @param \Cake\Event\Event $event Event object
	 * @param \Cake\ORM\Entity $entity Entity object
	 * @param \ArrayObject $options Options
	 * @uses Cake\Cache\Cache::clear()
	 * @uses setNextToBePublished()
	 */
	public function afterDelete(\Cake\Event\Event $event, \Cake\ORM\Entity $entity, \ArrayObject $options) {
		Cache::clear(FALSE, 'videos');	
		
		//Sets the next video to be published
		$this->setNextToBePublished();	
	}
	
	/**
	 * Called after an entity is saved.
	 * @param \Cake\Event\Event $event Event object
	 * @param \Cake\ORM\Entity $entity Entity object
	 * @param \ArrayObject $options Options
	 * @uses Cake\Cache\Cache::clear()
	 * @uses setNextToBePublished()
	 */
	public function afterSave(\Cake\Event\Event $event, \Cake\ORM\Entity $entity, \ArrayObject $options) {
		Cache::clear(FALSE, 'videos');
		
		//Sets the next video to be published
		$this->setNextToBePublished();
	}
	
    /**
     * Returns a rules checker object that will be used for validating application integrity
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules) {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['category_id'], 'Categories'));
        return $rules;
    }
	
	/**
	 * Checks if the cache is valid.
	 * If the cache is not valid, it empties the cache.
	 * @uses getNextToBePublished()
	 * @uses setNextToBePublished()
	 */
	public function checkIfCacheIsValid() {
		//Gets from cache the timestamp of the next record to be published
		$next = $this->getNextToBePublished();
		
		//If the cache is not valid, it empties the cache
		if($next && time() >= $next) {
			Cache::clear(FALSE, 'videos');
		
			//Sets the next record to be published
			$this->setNextToBePublished();
		}
	}
	
	/**
	 * Gets conditions from a filter form
	 * @param array $query Query (`$this->request->query`)
	 * @return array Conditions
	 * @uses MeCms\Model\Table\AppTable::fromFilter()
	 */
	public function fromFilter(array $query) {
		if(empty($query))
			return [];
		
		$conditions = parent::fromFilter($query);
		
		//"User" (author) field
		if(!empty($query['user']))
			$conditions[sprintf('%s.user_id', $this->alias())] = $query['user'];
		
		//"Category" field
		if(!empty($query['category']))
			$conditions[sprintf('%s.category_id', $this->alias())] = $query['category'];
		
		//"Is spot" field
		if(!empty($query['spot']))
			$conditions[sprintf('%s.is_spot', $this->alias())] = TRUE;
		
		return empty($conditions) ? [] : $conditions;
	}
	
	/**
	 * Gets from cache the timestamp of the next record to be published.
	 * This value can be used to check if the cache is valid
	 * @return int Timestamp
	 * @see checkIfCacheIsValid()
	 */
	public function getNextToBePublished() {
		return Cache::read('next_to_be_published', 'videos');
	}
	
	/**
	 * Gets random spots
	 * @param int $limit Limit
	 * @return array Spots
	 */
	public function getRandomSpots($limit = 1) {
		//Gets all spots
		$spots = $this->find('active')
			->select('youtube_id')
			->where(['is_spot' => TRUE])
			->cache('all_spots', 'videos')
			->toArray();
		
		//Shuffles
		shuffle($spots);
		
		//If the records are less than the limit, it returns all records
		return array_slice($spots, 0, count($spots) < $limit ? count($spots) : $limit);
	}
	
    /**
     * Initialize method
     * @param array $config The table configuration
     */
    public function initialize(array $config) {
        $this->table('youtube_videos');
        $this->displayField('title');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('CounterCache', ['Categories' => ['video_count']]);
        $this->belongsTo('Categories', [
            'foreignKey' => 'category_id',
            'className' => 'MeYoutube.VideosCategories'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'className' => 'MeCms.Users'
        ]);
    }
	
	/**
	 * Sets to cache the timestamp of the next record to be published.
	 * This value can be used to check if the cache is valid
	 * @see getNextToBePublished()
	 * @uses Cake\I18n\Time::toUnixString()
	 */
	public function setNextToBePublished() {
		$next = $this->find()
			->select('created')
			->where([
				sprintf('%s.active', $this->alias()) => TRUE,
				sprintf('%s.created >', $this->alias()) => new Time()
			])
			->order([sprintf('%s.created', $this->alias()) => 'ASC'])
			->first();
		
		Cache::write('next_to_be_published', empty($next->created) ? FALSE : $next->created->toUnixString(), 'posts');
	}

    /**
     * Default validation rules
     * @param \Cake\Validation\Validator $validator Validator instance
	 * @return \MeYoutube\Model\Validation\VideoValidator
	 */
    public function validationDefault(\Cake\Validation\Validator $validator) {
		return new \MeYoutube\Model\Validation\VideoValidator;
    }
}