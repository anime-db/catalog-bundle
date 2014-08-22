<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Console\Output;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use AnimeDb\Bundle\CatalogBundle\Console\Output\Decorator;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Export output to file
 *
 * @package AnimeDb\Bundle\CatalogBundle\Console\Output
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Export extends Decorator
{
    /**
     * File handle
     *
     * @var resource
     */
    protected $handle;

    /**
     * Append to end file
     *
     * @var boolean
     */
    protected $append;

    /**
     * Construct
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string $filename
     * @param boolean $append
     */
    public function __construct(OutputInterface $output, $filename, $append = true)
    {
        parent::__construct($output);
        $this->append = $append;

        $dir = pathinfo($filename, PATHINFO_DIRNAME);
        if (!is_dir($dir)) {
            if (true !== @mkdir($dir, 0755, true)) {
                throw new IOException('Failed to create the export directory: '.$dir);
            }
        }
        if (!($this->handle = @fopen($filename, 'w'))) {
            throw new IOException('Failed to open the export file: '.$filename);
        }
        if (!flock($this->handle, LOCK_EX)) {
            throw new IOException('Failed to lock the export file: '.$filename);
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Output\OutputInterface::write()
     */
    public function write($messages, $newline = false, $type = self::OUTPUT_NORMAL)
    {
        $this->writeToFile($messages, $newline);
        parent::write($messages, $newline, $type);
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Output\OutputInterface::writeln()
     */
    public function writeln($messages, $type = self::OUTPUT_NORMAL)
    {
        $this->writeToFile($messages, true);
        parent::writeln($messages, $type);
    }

    /**
     * Write messages to file
     *
     * @param string|array $messages
     * @param boolean $newline
     */
    protected function writeToFile($messages, $newline)
    {
        $messages = (array)$messages;
        foreach ($messages as $key => $message) {
            $messages[$key] = strip_tags($message).($newline ? PHP_EOL : '');
        }

        // reset file
        if (!$this->append) {
            rewind($this->handle);
            ftruncate($this->handle, 0);
        }

        fwrite($this->handle, implode('', $messages));
    }

    /**
     * Destruct
     */
    public function __destruct()
    {
        flock($this->handle, LOCK_UN);
        fclose($this->handle);
    }
}