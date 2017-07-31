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
namespace MeCmsYoutube\Model\Validation;

use MeCms\Model\Validation\AppValidator;

/**
 * Video validator class
 */
class VideoValidator extends AppValidator
{
    /**
     * Construct.
     *
     * Adds some validation rules.
     * @uses MeCms\Model\Validation\AppValidator::__construct()
     */
    public function __construct()
    {
        parent::__construct();

        //User (author)
        $this->requirePresence('user_id', 'create');

        //YouTube ID
        $this->add('youtube_id', [
            'validYoutubeId' => [
                'message' => __d('me_cms_youtube', 'You have to enter a valid {0} ID', 'YouTube'),
                'rule' => function ($value) {
                    return (bool)preg_match('/^[A-z0-9\-_]{11}$/', $value);
                },
            ],
        ])->requirePresence('youtube_id', 'create');

        //Category
        $this->add('category_id', [
            'naturalNumber' => [
                'message' => __d('me_cms', 'You have to select a valid option'),
                'rule' => 'naturalNumber',
            ],
        ])->requirePresence('category_id', 'create');

        //Title
        $this->requirePresence('title', 'create');

        //Text
        $this->requirePresence('text', 'create');

        //"Is spot"
        $this->add('is_spot', [
            'boolean' => [
                'message' => __d('me_cms', 'You have to select a valid option'),
                'rule' => 'boolean',
            ],
        ]);

        //Seconds
        $this->add('seconds', [
            'naturalNumber' => [
                'message' => __d('me_cms', 'You have to enter a valid value'),
                'rule' => 'naturalNumber',
            ],
        ])
        ->allowEmpty('seconds');

        //Duration
        $this->add('duration', [
            'validDuration' => [
                'message' => __d('me_cms', 'You have to enter a valid value'),
                'rule' => function ($value) {
                    return (bool)preg_match('/^(\d{2}:){1,2}\d{2}$/', $value);
                },
            ],
        ])
        ->allowEmpty('duration');
    }
}
