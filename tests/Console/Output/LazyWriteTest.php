<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Console\Output;

use AnimeDb\Bundle\CatalogBundle\Console\Output\LazyWrite;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test output decorator
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Console\Output
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class LazyWriteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Output
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $output;

    /**
     * LazyWrite
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Console\Output\LazyWrite
     */
    protected $lazy_write;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->output = $this->getMock('\Symfony\Component\Console\Output\OutputInterface');
        $this->lazy_write = new LazyWrite($this->output);
    }

    /**
     * Get newline types
     *
     * @return array
     */
    public function getNewlineTypes()
    {
        $params = [];
        foreach ($this->getTypes() as $type) {
            $params[] = [true, $type[0]];
            $params[] = [false, $type[0]];
        }
        return $params;
    }

    /**
     * Test write
     *
     * @dataProvider getNewlineTypes
     *
     * @param boolean $newline
     * @param integer $type
     */
    public function testWrite($newline, $type)
    {
        $this->output
            ->expects($this->once())
            ->method('write')
            ->with('foo', $newline, $type);
        $this->lazy_write->setLazyWrite(false);
        $this->lazy_write->write('foo', $newline, $type);
    }

    /**
     * Get types
     *
     * @return array
     */
    public function getTypes()
    {
        return [
            [OutputInterface::OUTPUT_NORMAL],
            [OutputInterface::OUTPUT_PLAIN],
            [OutputInterface::OUTPUT_RAW],
        ];
    }

    /**
     * Test write ln
     *
     * @dataProvider getTypes
     *
     * @param integer $type
     */
    public function testWriteLn($type)
    {
        $this->output
            ->expects($this->once())
            ->method('writeln')
            ->with('foo', $type);
        $this->lazy_write->setLazyWrite(false);
        $this->lazy_write->writeln('foo', $type);
    }

    /**
     * Get messages newline types
     *
     * @return array
     */
    public function getMessagesNewlineTypes()
    {
        $params = [];
        foreach ($this->getMessagesTypes() as $type) {
            $params[] = [$type[0], true, $type[1]];
            $params[] = [$type[0], false, $type[1]];
        }
        return $params;
    }

    /**
     * Test write no write
     *
     * @dataProvider getMessagesNewlineTypes
     *
     * @param string|array $messages
     * @param boolean $newline
     * @param integer $type
     */
    public function testWriteNoWrite($messages, $newline, $type)
    {
        $this->output
            ->expects($this->never())
            ->method('writeln');
        $this->lazy_write->write($messages, $type);
    }

    /**
     * Test write lazy
     *
     * @dataProvider getMessagesNewlineTypes
     *
     * @param string|array $messages
     * @param boolean $newline
     * @param integer $type
     */
    public function testWriteLazy($messages, $newline, $type)
    {
        $_m = (array)$messages;
        foreach ($_m as $index => $message) {
            $this->output
                ->expects($this->at($index))
                ->method('writeln')
                ->with($message.($newline ? PHP_EOL : ''), $type);
        }
        $this->lazy_write->write($messages, $newline, $type);
        $this->lazy_write->writeAll();
    }

    /**
     * Get messages types
     *
     * @return array
     */
    public function getMessagesTypes()
    {
        $params = [];
        foreach ($this->getTypes() as $type) {
            $params[] = ['foo', $type[0]];
            $params[] = [['foo', 'bar'], $type[0]];
        }
        return $params;
    }

    /**
     * Test write ln no write
     *
     * @dataProvider getMessagesTypes
     *
     * @param string|array $messages
     * @param integer $type
     */
    public function testWriteLnNoWrite($messages, $type)
    {
        $this->output
            ->expects($this->never())
            ->method('writeln');
        $this->lazy_write->writeln($messages, $type);
    }

    /**
     * Test write ln lazy
     *
     * @dataProvider getMessagesTypes
     *
     * @param string|array $messages
     * @param integer $type
     */
    public function testWriteLnLazy($messages, $type)
    {
        $_m = (array)$messages;
        foreach ($_m as $index => $message) {
            $this->output
                ->expects($this->at($index))
                ->method('writeln')
                ->with($message, $type);
        }
        $this->lazy_write->writeln($messages, $type);
        $this->lazy_write->writeAll();
    }

    /**
     * Test lazy write
     */
    public function testLazyWrite()
    {
        $this->assertTrue($this->lazy_write->isLazyWrite());
        $this->lazy_write->setLazyWrite(false);
        $this->assertFalse($this->lazy_write->isLazyWrite());
    }
}
