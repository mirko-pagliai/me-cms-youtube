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

use MeTools\Shell\InstallShell as BaseInstallShell;

/**
 * Executes some tasks to make the system ready to work
 */
class InstallShell extends BaseInstallShell
{
    /**
     * Construct
     * @param \Cake\Console\ConsoleIo|null $io An io instance
     * @uses $config
     * @uses MeTools\Shell\InstallShell::__construct()
     */
    public function __construct(\Cake\Console\ConsoleIo $io = null)
    {
        parent::__construct($io);

        //Configuration files to be copied
        $this->config = [
            'MeCmsYoutube.me_youtube',
        ];
    }

    /**
     * Executes all available tasks
     * @uses MeTools\Shell\InstallShell::copyConfig()
     * @return void
     */
    public function all()
    {
        if ($this->param('force')) {
            $this->copyConfig();

            return;
        }

        $ask = $this->in(__d('me_tools', 'Copy configuration files?'), ['Y', 'n'], 'Y');
        if (in_array($ask, ['Y', 'y'])) {
            $this->copyConfig();
        }
    }

    /**
     * Gets the option parser instance and configures it.
     * @return ConsoleOptionParser
     * @uses MeTools\Shell\InstallShell::getOptionParser()
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        //Resets subcommands
        foreach (array_keys($parser->toArray()['subcommands']) as $subcommand) {
            $parser->removeSubcommand($subcommand);
        }

        $parser->addSubcommand('all', ['help' => __d('me_tools', 'Executes all available tasks')]);
        $parser->addSubcommand('copyConfig', ['help' => __d('me_tools', 'Copies the configuration files')]);

        return $parser;
    }
}
