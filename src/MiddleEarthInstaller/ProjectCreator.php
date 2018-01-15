<?php

namespace EnderLab\MiddleEarth\Installer;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Script\Event;

class ProjectCreator
{
    /**
     * @var IOInterface
     */
    private $io;

    /**
     * @var Composer
     */
    private $composer;

    private $config;

    private $rootPath;

    private static $logo = '
-------------------------------------------------------------------------------------------------------------
||                                                                                                         ||
||   /$$      /$$ /$$       /$$       /$$ /$$           /$$$$$$$$                       /$$     /$$        ||
||  | $$$    /$$$|__/      | $$      | $$| $$          | $$_____/                      | $$    | $$        ||
||  | $$$$  /$$$$ /$$  /$$$$$$$  /$$$$$$$| $$  /$$$$$$ | $$        /$$$$$$   /$$$$$$  /$$$$$$  | $$$$$$$   ||
||  | $$ $$/$$ $$| $$ /$$__  $$ /$$__  $$| $$ /$$__  $$| $$$$$    |____  $$ /$$__  $$|_  $$_/  | $$__  $$  ||
||  | $$  $$$| $$| $$| $$  | $$| $$  | $$| $$| $$$$$$$$| $$__/     /$$$$$$$| $$  \__/  | $$    | $$  \ $$  ||
||  | $$\  $ | $$| $$| $$  | $$| $$  | $$| $$| $$_____/| $$       /$$__  $$| $$        | $$ /$$| $$  | $$  ||
||  | $$ \/  | $$| $$|  $$$$$$$|  $$$$$$$| $$|  $$$$$$$| $$$$$$$$|  $$$$$$$| $$        |  $$$$/| $$  | $$  ||
||  |__/     |__/|__/ \_______/ \_______/|__/ \_______/|________/ \_______/|__/         \___/  |__/  |__/  ||
||                                                                                                         ||
-------------------------------------------------------------------------------------------------------------
';

    /**
     * @todo project creator a mettre dans le depot skeleton
     * @todo package installer dans framework uniquement pour l'ajout d'un nouveau package
     * @todo voir pour poser des questions
     */

    public static function createProject(Event $event)
    {
        $event->getIO()->write('<info>' . self::$logo . '</info>');
        $installer = new self($event->getIO(), $event->getComposer());

        $event->getIO()->write('Creation directory tree');
        $installer->createDirectories();

        $event->getIO()->write('Creation configuration files');
        $installer->createConfigFiles();
    }

    public static function postCreateProject(Event $event)
    {
        // @todo clean directories
    }

    public function __construct(IOInterface $io, Composer $composer)
    {
        $this->io = $io;
        $this->composer = $composer;
        $this->rootPath = rtrim(realpath(dirname(Factory::getComposerFile())), '/').'/';
        $this->config = include __DIR__ . '/config/config.php';
    }

    public function createDirectories(bool $verbose = true)
    {
        foreach ($this->config['directories'] as $directory) {
            if (!is_dir($this->rootPath . $directory)) {
                if (true == mkdir($this->rootPath . $directory)) {
                    $this->io->write("\t".'- [<info>OK</info>] Create directory "<info>' . $directory . '</info>".');
                } else {
                    $this->io->write("\t".'- [<error>ERR</error>] Cannot create directory "<error>' . $directory . '</error>".');
                }
            }
        }
    }

    public function createConfigFiles(bool $verbose = true)
    {
        foreach ($this->config['template-file'] as $source => $dest) {
            if (!is_file($this->rootPath . $dest)) {
                if (true == copy(__DIR__ . '/' . $source, $this->rootPath . $dest)) {
                    $this->io->write("\t".'- [<info>OK</info>] Create file "<info>' . $dest . '</info>".');
                } else {
                    $this->io->write("\t".'- [<error>ERR</error>] Cannot create file "<error>' . $dest . '</error>".');
                }
            }
        }
    }
}
