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
use AnimeDb\Bundle\CatalogBundle\Console\Output\Decorator;

/**
 * Export output to file
 *
 * @package AnimeDb\Bundle\CatalogBundle\Console\Output
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Export extends Decorator
{
    /**
     * Filename
     *
     * @var string
     */
    protected $filename;

    /**
     * Flags
     *
     * @var integer
     */
    protected $flags;

    /**
     * Construct
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string $filename
     * @param integer $flags
     */
    public function __construct(OutputInterface $output, $filename, $flags = FILE_APPEND)
    {
        parent::__construct($output);
        $this->filename = $filename;
        $this->flags = $flags;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Output\OutputInterface::write()
     */
    public function write($messages, $newline = false, $type = self::OUTPUT_NORMAL)
    {
        file_put_contents($this->filename, implode($newline ? PHP_EOL : '', (array)$messages), $this->flags);
        parent::write($messages, $newline, $type);
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Output\OutputInterface::writeln()
     */
    public function writeln($messages, $type = self::OUTPUT_NORMAL)
    {
        file_put_contents($this->filename, implode('', (array)$messages), $this->flags);
        parent::writeln($messages, $type);
    }
}