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

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use MeYoutube\Model\Entity\Video;

/**
 * Videos model
 */
class YoutubeVideosTable extends Table {
    /**
     * Initialize method
     * @param array $config The table configuration
     */
    public function initialize(array $config) {
        $this->table('youtube_videos');
        $this->displayField('title');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'className' => 'MeYoutube.Users'
        ]);
        $this->belongsTo('Youtubes', [
            'foreignKey' => 'youtube_id',
            'joinType' => 'INNER',
            'className' => 'MeYoutube.Youtubes'
        ]);
        $this->belongsTo('Categories', [
            'foreignKey' => 'category_id',
            'joinType' => 'INNER',
            'className' => 'MeYoutube.Categories'
        ]);
    }

    /**
     * Default validation rules
     * @param \Cake\Validation\Validator $validator Validator instance
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator) {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create')
            ->requirePresence('title', 'create')
            ->notEmpty('title')
            ->allowEmpty('subtitle')
            ->allowEmpty('description')
            ->add('priority', 'valid', ['rule' => 'numeric'])
            ->requirePresence('priority', 'create')
            ->notEmpty('priority')
            ->add('active', 'valid', ['rule' => 'boolean'])
            ->requirePresence('active', 'create')
            ->notEmpty('active')
            ->add('is_spot', 'valid', ['rule' => 'boolean'])
            ->requirePresence('is_spot', 'create')
            ->notEmpty('is_spot');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating application integrity
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules) {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['youtube_id'], 'Youtubes'));
        $rules->add($rules->existsIn(['category_id'], 'Categories'));
        return $rules;
    }
}