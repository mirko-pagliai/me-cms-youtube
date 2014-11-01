<?php
/**
 * YoutubeVideo.
 *
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
 * @copyright	Copyright (c) 2014, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeYoutube\Model
 */

App::uses('MeCmsAppModel', 'MeCms.Model');
App::uses('CakeTime', 'Utility');

/**
 * YoutubeVideo Model
 */
class YoutubeVideo extends MeCmsAppModel {
	/**
	 * Display field
	 * @var string
	 */
	public $displayField = 'title';

	/**
	 * Validation rules
	 * @var array
	 */
	public $validate = array(
		'user_id' => array(
			'message'	=> 'You have to select an option',
			'rule'		=> array('naturalNumber')
		),
		'title' => array(
			'between' => array(
				'last'		=> FALSE,
				'message'	=> 'Must be between %d and %d chars',
				'rule'		=> array('between', 3, 100)
			),
			'isUnique' => array(
				'message'	=> 'This value is already used',
				'rule'		=> 'isUnique'
			)
		),
		'subtitle' => array(
			'allowEmpty'	=> TRUE,
			'message'		=> 'Must be at most %d chars',
			'rule'			=> array('maxLength', 150)
		),
		'youtube_id' => array(
			'message'	=> 'This value is already used',
			'rule'		=> 'isUnique'
		),
		'description' => array(
			'allowEmpty'	=> TRUE,
			'message'		=> 'Must be at most %d chars',
			'rule'			=> array('maxLength', 255)
		),
		'priority' => array(
			'message'	=> 'You have to select a valid option',
			'rule'		=> array('range', 0, 6)
		),
		'active' => array(
			'message'	=> 'You have to select a valid option',
			'rule'		=> 'boolean'
		),
		'is_spot' => array(
			'message'	=> 'You have to select a valid option',
			'rule'		=> 'boolean'
		),
		'created' => array(
			'allowEmpty'	=> TRUE,
			'message'		=> 'Must be a valid datetime',
			'rule'			=> 'datetime'
		),
		'modified' => array(
			'message'	=> 'Must be a valid datetime',
			'rule'		=> 'datetime'
		)
	);

	/**
	 * belongsTo associations
	 * @var array
	 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'MeCms.User',
			'foreignKey' => 'user_id'
		)
	);
	
	/**
	 * "Active" find method. It finds for active records.
	 * @param string $state Either "before" or "after"
	 * @param array $query
	 * @param array $results
	 * @return mixed Query or results
	 */
	protected function _findActive($state, $query, $results = array()) {
        if($state === 'before') {			
			$query['conditions'] = empty($query['conditions']) ? array() : $query['conditions'];
			
			//Only active items
			$query['conditions'][$this->alias.'.active'] = TRUE;
			//No spot videos
			$query['conditions'][$this->alias.'.is_spot'] = FALSE;
			//Only items published in the past
			$query['conditions'][$this->alias.'.created <='] = date('Y-m-d H:i:s');
			
            return $query;
        }
		
		if($query['limit'] === 1 && !empty($results[0]))
			return $results[0];
		
        return $results;
    }
	
	/**
	 * Called after each successful save operation.
	 * @param boolean $created TRUE if this save created a new record
	 * @param array $options Options passed from Model::save()
	 */
	public function afterSave($created, $options = array()) {
		Cache::clearGroup('videos', 'videos');
	}
	
	/**
	 * Called before each save operation, after validation. Return a non-true result to halt the save.
	 * @param array $options Options passed from Model::save()
	 * @return boolean TRUE if the operation should continue, FALSE if it should abort
	 */
	public function beforeSave($options = array()) {
		//If the creation datetime isn't set, then it is the current datetime
		if(empty($this->data[$this->alias]['created']))
			$this->data[$this->alias]['created'] = CakeTime::format(time(), '%Y-%m-%d %H:%M');
		
		return TRUE;
	}
	
	/**
	 * Checks whether a post belongs to a user.
	 * @param int $id Post id
	 * @param int $user_id User id
	 * @return bool TRUE if the post belongs to the user, otherwise FALSE
	 */
	public function isOwnedBy($id, $user_id) {
		return $this->field('id', compact('id', 'user_id')) !== FALSE;
	}
}