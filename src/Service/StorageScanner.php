<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service;

use Symfony\Component\Filesystem\Filesystem;
use AnimeDb\Bundle\AppBundle\Service\CommandExecutor;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage as StorageEntity;

/**
 * Storage scanner service
 *
 * @package AnimeDb\Bundle\CatalogBundle\Service
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class StorageScanner
{
    /**
     * Command
     *
     * @var \AnimeDb\Bundle\AppBundle\Service\CommandExecutor
     */
    protected $command;

    /**
     * Filesystem
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * Output file
     *
     * @var string
     */
    protected $output = '';

    /**
     * Progress file
     *
     * @var string
     */
    protected $progress = '';

    /**
     * Construct
     *
     * @param \AnimeDb\Bundle\AppBundle\Service\CommandExecutor $command
     * @param \Symfony\Component\Filesystem\Filesystem $fs
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
     * Scan storage in background and export progress and output
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     */
    public function export(StorageEntity $storage)
    {
        $output = sprintf($this->output, $storage->getId());
        $progress = sprintf($this->progress, $storage->getId());

        $this->fs->mkdir([dirname($output), dirname($progress)], 0755);

        $this->command->send(sprintf(
            'php app/console animedb:scan-storage --no-ansi --export=%s %s >%s 2>&1',
            $progress,
            $storage->getId(),
            $output
        ));
    }
}
