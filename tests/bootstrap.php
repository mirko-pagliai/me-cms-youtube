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
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Routing\DispatcherFactory;

require dirname(__DIR__) . '/vendor/autoload.php';

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

// Path constants to a few helpful things.
define('ROOT', dirname(__DIR__) . DS);
define('VENDOR', ROOT . 'vendor' . DS);
define('CAKE_CORE_INCLUDE_PATH', ROOT . 'vendor' . DS . 'cakephp' . DS . 'cakephp');
define('CORE_PATH', ROOT . 'vendor' . DS . 'cakephp' . DS . 'cakephp' . DS);
define('CAKE', CORE_PATH . 'src' . DS);
define('TESTS', ROOT . 'tests' . DS);
define('TEST_APP', TESTS . 'test_app' . DS);
define('APP', TEST_APP . 'TestApp' . DS);
define('APP_DIR', 'TestApp');
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', APP . 'webroot' . DS);
define('TMP', sys_get_temp_dir() . DS);
define('CONFIG', APP . 'config' . DS);
define('CACHE', TMP);
define('LOGS', TMP);
define('SESSIONS', TMP . 'sessions' . DS);

//@codingStandardsIgnoreStart
@mkdir(LOGS);
@mkdir(SESSIONS);
@mkdir(CACHE);
@mkdir(CACHE . 'views');
@mkdir(CACHE . 'models');
//@codingStandardsIgnoreEnd

require CORE_PATH . 'config' . DS . 'bootstrap.php';

date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');

Configure::write('debug', true);
Configure::write('App', [
    'namespace' => 'App',
    'encoding' => 'UTF-8',
    'base' => false,
    'baseUrl' => false,
    'dir' => APP_DIR,
    'webroot' => 'webroot',
    'wwwRoot' => WWW_ROOT,
    'fullBaseUrl' => 'http://localhost',
    'imageBaseUrl' => 'img/',
    'jsBaseUrl' => 'js/',
    'cssBaseUrl' => 'css/',
    'paths' => [
        'plugins' => [APP . 'Plugin' . DS],
        'templates' => [APP . 'TestApp' . DS . 'Template' . DS],
    ]
]);

Cache::config([
    '_cake_core_' => [
        'engine' => 'File',
        'prefix' => 'cake_core_',
        'serialize' => true,
    ],
    '_cake_model_' => [
        'engine' => 'File',
        'prefix' => 'cake_model_',
        'serialize' => true,
    ],
    'default' => [
        'engine' => 'File',
        'prefix' => 'default_',
        'serialize' => true,
    ],
]);

// Ensure default test connection is defined
if (!getenv('db_dsn')) {
    putenv('db_dsn=sqlite://127.0.0.1/' . TMP . 'debug_kit_test.sqlite');
}
$config = [
    'url' => getenv('db_dsn'),
    'timezone' => 'UTC',
];

// Use the test connection for 'debug_kit' as well.
ConnectionManager::config('test', $config);

Configure::write('Session', [
    'defaults' => 'php'
]);

Configure::write('Assets.target', TMP . 'assets');

//@codingStandardsIgnoreLine
@mkdir(Configure::read('Assets.target'));

/**
 * Loads plugins
 */
Plugin::load('Assets', [
    'bootstrap' => true,
    'path' => VENDOR . 'mirko-pagliai' . DS . 'assets' . DS,
]);
Plugin::load('MeTools', [
    'bootstrap' => true,
    'path' => VENDOR . 'mirko-pagliai' . DS . 'me-tools' . DS,
]);

define('LOGIN_RECORDS', TMP . 'login' . DS);
//@codingStandardsIgnoreLine
@mkdir(LOGIN_RECORDS);

define('UPLOADED', WWW_ROOT . 'files' . DS);
//@codingStandardsIgnoreLine
@mkdir(UPLOADED);

Plugin::load('MeCms', [
    'bootstrap' => false, //Doesn't load the bootstrap
    'path' => VENDOR . 'mirko-pagliai' . DS . 'me-cms' . DS,
    'routes' => true,
]);

require_once VENDOR . 'mirko-pagliai' . DS . 'me-cms' . DS . 'config' . DS . 'bootstrap_base.php';

Plugin::load('MeCmsYoutube', [
    'bootstrap' => true,
    'path' => ROOT,
    'routes' => true,
]);

DispatcherFactory::add('Routing');
DispatcherFactory::add('ControllerFactory');

ini_set('intl.default_locale', 'en_US');
