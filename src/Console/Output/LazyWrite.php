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

use AnimeDb\Bundle\CatalogBundle\Console\Output\Decorator;

/**
 * Lazy write output
 *
 * @package AnimeDb\Bundle\CatalogBundle\Console\Output
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class LazyWrite extends Decorator
{
    /**
     * Write stack
     *
     * @var array
     */
    protected $stack = [];

    /**
     * Save messages in stack
     *
     * @var boolean
     */
    protected $lazy_write = true;

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Output\OutputInterface::write()
     */
    public function write($messages, $newline = false, $type = self::OUTPUT_NORMAL)
    {
        if ($this->lazy_write) {
            $messages = (array)$messages;
            foreach ($messages as $message) {
                $this->stack[] = [$message.($newline ? PHP_EOL : ''), $type];
            }
        } else {
            parent::write($messages, $newline, $type);
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Output\OutputInterface::writeln()
     */
    public function writeln($messages, $type = self::OUTPUT_NORMAL)
    {
        if ($this->lazy_write) {
            $messages = (array)$messages;
            foreach ($messages as $message) {
                $this->stack[] = [$message, $type];
            }
        } else {
            parent::writeln($messages, $type);
        }
    }

    /**
     * Write all messages from stack
     */
    public function writeAll()
    {
        while ($message = array_shift($this->stack)) {
            parent::writeln($message[0], $message[1]);
        }
    }

    /**
     * Is lazy write
     *
     * @return boolean
     */
    public function isLazyWrite()
    {
        return $this->lazy_write;
    }

    /**
     * Set lazy write
     *
     * @param boolean $lazy_write
     */
    public function setLazyWrite($lazy_write)
    {
        $this->lazy_write = $lazy_write;
    }
}
