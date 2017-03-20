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
namespace MeCmsYoutube\Shell;

use MeCmsYoutube\Utility\Youtube;
use MeCms\Console\BaseUpdateShell;

/**
 * Applies updates
 */
class UpdateShell extends BaseUpdateShell
{
    /**
     * Internal method. It updates all records, fixing those that have the
     *  `duration` or `seconds` field empty
     * @return void
     * @throws \Exception
     * @uses MeCmsYoutube\Utility\Youtube::getInfo()
     */
    protected function _updateAllRecordsDurationField()
    {
        $this->loadModel('MeCmsYoutube.Videos');

        $videos = $this->Videos->find('all')
            ->select(['id', 'youtube_id', 'duration', 'seconds'])
            ->where(['OR' => [
                ['duration' => '00:00'],
                ['duration' => ''],
                ['seconds' => 0],
            ]])
            ->toArray();

        if (empty($videos)) {
            $this->verbose('No record to update');

            return;
        }

        $this->verbose(sprintf('%s records to be updated', count($videos)));

        foreach ($videos as $video) {
            try {
                $data = (new Youtube)->getInfo($video->youtube_id);

                if (empty($data->duration)) {
                    throw new \Exception(sprintf('Can\'t find duration value for record %s', $video->id));
                }

                if (empty($data->seconds)) {
                    throw new \Exception(sprintf('Can\'t find second value for record %s', $video->id));
                }

                $this->Videos->query()->update()
                    ->set(['duration' => $data->duration, 'seconds' => $data->seconds])
                    ->where(['id' => $video->id])
                    ->execute();
            } catch (\Exception $e) {
                if (substr_count($e->getMessage(), 'fopen(): php_network_getaddresses')) {
                    $this->err(sprintf('Temporary failure in name resolution for record %s', $video->id));
                } elseif (substr_count($e->getMessage(), 'failed to open stream: Connection timed out')) {
                    $this->err(sprintf('Connection timed out for record %s', $video->id));
                } else {
                    $this->err(sprintf('%s for record %s', $e->getMessage(), $video->id));
                }
            }
        }
    }

    /**
     * Updates to 2.7.0 version
     * @return void
     * @uses _updateAllRecordsDurationField()
     */
    public function to2v7v0()
    {
        $this->_updateAllRecordsDurationField();
    }

    /**
     * Updates to 2.4.0 version
     * @return void
     * @uses MeCms\Console\BaseUpdateShell::$connection
     */
    public function to2v4v0()
    {
        $this->loadModel('MeCmsYoutube.Videos');

        //Renames the "description" field as "text"
        if ($this->_checkColumn('description', $this->Videos->getTable())) {
            $this->connection->execute(sprintf(
                'ALTER TABLE `%s` CHANGE `description` `text` TEXT null DEFAULT null;',
                $this->Videos->getTable()
            ));
        }
    }

    /**
     * Updates to 2.3.0 version
     * @return void
     * @uses MeCms\Console\BaseUpdateShell::$connection
     * @uses MeCms\Console\BaseUpdateShell::_checkColumn()
     */
    public function to2v3v0()
    {
        $this->loadModel('MeCmsYoutube.VideosCategories');

        //Adds "created" field to the videos categories table and sets the default value
        if (!$this->_checkColumn('created', $this->VideosCategories->getTable())) {
            $this->connection->execute(sprintf(
                'ALTER TABLE `%s` ADD `created` DATETIME null AFTER `video_count`;',
                $this->VideosCategories->getTable()
            ));
            $this->VideosCategories->query()->update()->set(['created' => $this->now])->execute();
        }

        //Adds "modified" field to the videos categories table and sets the default value
        if (!$this->_checkColumn('modified', $this->VideosCategories->getTable())) {
            $this->connection->execute(sprintf(
                'ALTER TABLE `%s` ADD `modified` DATETIME null AFTER `created`;',
                $this->VideosCategories->getTable()
            ));
            $this->VideosCategories->query()->update()->set(['modified' => $this->now])->execute();
        }
    }

    /**
     * Updates to 2.0.4-RC4 version
     * @return void
     * @uses _updateAllRecordsDurationField()
     */
    public function to2v0v4vRC4()
    {
        $this->_updateAllRecordsDurationField();
    }
}
