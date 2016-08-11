<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Console\Output;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

/**
 * Decorator for output.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Decorator implements OutputInterface
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param array|string $messages
     * @param bool|false $newline
     * @param int $type
     */
    public function write($messages, $newline = false, $type = self::OUTPUT_NORMAL)
    {
        $this->output->write($messages, $newline, $type);
    }

    /**
     * @param array|string $messages
     * @param int $type
     */
    public function writeln($messages, $type = self::OUTPUT_NORMAL)
    {
        $this->output->writeln($messages, $type);
    }

    /**
     * @param int $level
     */
    public function setVerbosity($level)
    {
        $this->output->setVerbosity($level);
    }

    /**
     * @return int
     */
    public function getVerbosity()
    {
        return $this->output->getVerbosity();
    }

    /**
     * @param bool $decorated
     */
    public function setDecorated($decorated)
    {
        $this->output->setDecorated($decorated);
    }

    /**
     * @return bool
     */
    public function isDecorated()
    {
        return $this->output->isDecorated();
    }

    /**
     * @param OutputFormatterInterface $formatter
     */
    public function setFormatter(OutputFormatterInterface $formatter)
    {
        $this->output->setFormatter($formatter);
    }

    /**
     * @return OutputFormatterInterface
     */
    public function getFormatter()
    {
        return $this->output->getFormatter();
    }
}
