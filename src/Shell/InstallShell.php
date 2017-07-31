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
namespace MeCmsYoutube\Shell;

use Cake\Console\ConsoleIo;
use MeCms\Shell\InstallShell as BaseInstallShell;

/**
 * Executes some tasks to make the system ready to work
 */
class InstallShell extends BaseInstallShell
{
    /**
     * Construct
     * @param \Cake\Console\ConsoleIo|null $io An io instance
     * @uses $config
     * @uses MeCms\Shell\InstallShell::__construct()
     */
    public function __construct(ConsoleIo $io = null)
    {
        parent::__construct($io);

        //Configuration files to be copied
        $this->config = [
            'MeCmsYoutube.me_cms_youtube',
        ];
    }

    /**
     * Executes all available tasks
     * @uses MeCms\Shell\InstallShell::copyConfig()
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
     * @uses MeCms\Shell\InstallShell::getOptionParser()
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
