<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service\Storage;

use Symfony\Component\Filesystem\Filesystem;
use AnimeDb\Bundle\AppBundle\Service\CommandExecutor;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage as StorageEntity;

/**
 * Storage scanner service.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ScanExecutor
{
    /**
     * @var CommandExecutor
     */
    protected $command;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $output = '';

    /**
     * @var string
     */
    protected $progress = '';

    /**
     * @param CommandExecutor $command
     * @param Filesystem $fs
     * @param string $output
     * @param string $progress
     */
    public function __construct(CommandExecutor $command, Filesystem $fs, $output, $progress)
    {
        $this->progress = $progress;
        $this->command = $command;
        $this->output = $output;
        $this->fs = $fs;
    }

    /**
     * Scan storage in background and export progress and output.
     *
     * @param StorageEntity $storage
     */
    public function export(StorageEntity $storage)
    {
        $output = sprintf($this->output, $storage->getId());
        $progress = sprintf($this->progress, $storage->getId());

        $this->fs->mkdir([dirname($output), dirname($progress)], 0755);

        $this->command->send(sprintf(
            'php app/console animedb:scan-storage --no-ansi --force --export=%s %s >%s 2>&1',
            $progress,
            $storage->getId(),
            $output
        ));
    }
}
