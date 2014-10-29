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

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test output decorator
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Console\Output
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class DecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Output
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $output;

    /**
     * Decorator
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $decorator;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->output = $this->getMock('\Symfony\Component\Console\Output\OutputInterface');
        $this->decorator = $this->getMockForAbstractClass('AnimeDb\Bundle\CatalogBundle\Console\Output\Decorator', [$this->output]);
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
        $this->decorator->write('foo', $newline, $type);
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
        $this->decorator->writeln('foo', $type);
    }

    /**
     * Get set methods
     *
     * @return array
     */
    public function getSetMethods()
    {
        return [
            ['setVerbosity', 123],
            ['setDecorated', true],
            ['setDecorated', false],
            ['setFormatter', $this->getMock('\Symfony\Component\Console\Formatter\OutputFormatterInterface')]
        ];
    }

    /**
     * Test setters
     *
     * @dataProvider getSetMethods
     * 
     * @param string $method
     * @param string $data
     */
    public function testSet($method, $data)
    {
        $this->output
            ->expects($this->once())
            ->method($method)
            ->with($data);
        call_user_func([$this->decorator, $method], $data);
    }

    /**
     * Get get methods
     *
     * @return array
     */
    public function getGetMethods()
    {
        return [
            ['getVerbosity', 123],
            ['isDecorated', true],
            ['isDecorated', false],
            ['getFormatter', $this->getMock('\Symfony\Component\Console\Formatter\OutputFormatterInterface')]
        ];
    }

    /**
     * Test getters
     *
     * @dataProvider getGetMethods
     * 
     * @param string $method
     * @param string $data
     */
    public function testGetVerbosity($method, $data)
    {
        $this->output
            ->expects($this->once())
            ->method($method)
            ->willReturn($data);
        $this->assertEquals($data, call_user_func([$this->decorator, $method]));
    }
}
