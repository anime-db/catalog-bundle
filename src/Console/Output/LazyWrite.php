<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Console\Output;

/**
 * Lazy write output.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class LazyWrite extends Decorator
{
    /**
     * Write stack.
     *
     * @var array
     */
    protected $stack = [];

    /**
     * Save messages in stack.
     *
     * @var bool
     */
    protected $lazy_write = true;

    /**
     * @param array|string $messages
     * @param bool|false $newline
     * @param int $type
     */
    public function write($messages, $newline = false, $type = self::OUTPUT_NORMAL)
    {
        if ($this->lazy_write) {
            $messages = (array) $messages;
            foreach ($messages as $message) {
                $this->stack[] = [$message.($newline ? PHP_EOL : ''), $type];
            }
        } else {
            parent::write($messages, $newline, $type);
        }
    }

    /**
     * @param array|string $messages
     * @param int $type
     */
    public function writeln($messages, $type = self::OUTPUT_NORMAL)
    {
        if ($this->lazy_write) {
            $messages = (array) $messages;
            foreach ($messages as $message) {
                $this->stack[] = [$message, $type];
            }
        } else {
            parent::writeln($messages, $type);
        }
    }

    /**
     * Write all messages from stack.
     */
    public function writeAll()
    {
        while ($message = array_shift($this->stack)) {
            parent::writeln($message[0], $message[1]);
        }
    }

    /**
     * @return bool
     */
    public function isLazyWrite()
    {
        return $this->lazy_write;
    }

    /**
     * @param bool $lazy_write
     */
    public function setLazyWrite($lazy_write)
    {
        $this->lazy_write = $lazy_write;
    }
}
