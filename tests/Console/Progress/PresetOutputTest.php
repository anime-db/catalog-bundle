<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Console\Progress;

use AnimeDb\Bundle\CatalogBundle\Console\Progress\PresetOutput;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test progress preset output.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class PresetOutputTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ProgressHelper
     */
    protected $progress;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|OutputInterface
     */
    protected $output;

    /**
     * @var \AnimeDb\Bundle\CatalogBundle\Console\Progress\PresetOutput
     */
    protected $preset_output;

    protected function setUp()
    {
        $this->progress = $this->getMock('\Symfony\Component\Console\Helper\ProgressHelper');
        $this->output = $this->getMock('\Symfony\Component\Console\Output\OutputInterface');
        $this->preset_output = new PresetOutput($this->progress, $this->output);
    }

    /**
     * @return array
     */
    public function getGetMethods()
    {
        return [
            ['getHelperSet', $this->getMock('\Symfony\Component\Console\Helper\HelperSet')],
            ['getName', 'foo'],
        ];
    }

    /**
     * @dataProvider getGetMethods
     *
     * @param string $method
     * @param string $data
     */
    public function testGet($method, $data)
    {
        $this->progress
            ->expects($this->once())
            ->method($method)
            ->will($this->returnValue($data));
        $this->assertEquals($data, call_user_func([$this->preset_output, $method]));
    }

    /**
     * @return array
     */
    public function getSetMethods()
    {
        return [
            ['setHelperSet', [null]],
            ['setHelperSet', [$this->getMock('\Symfony\Component\Console\Helper\HelperSet')]],
            ['setBarWidth', [132]],
            ['setBarCharacter', ['foo']],
            ['setEmptyBarCharacter', ['foo']],
            ['setProgressCharacter', ['foo']],
            ['setFormat', ['foo']],
            ['setRedrawFrequency', [123]],
            ['advance', [1, false]],
            ['advance', [123, true]],
            ['setCurrent', [1, true]],
            ['setCurrent', [123, false]],
            ['display', [true]],
            ['display', [false]],
            ['finish', []],
        ];
    }

    /**
     * @dataProvider getSetMethods
     *
     * @param string $method
     * @param array $params
     */
    public function testSet($method, array $params)
    {
        $mock = $this->progress
            ->expects($this->once())
            ->method($method);
        call_user_func_array([$mock, 'with'], $params);
        call_user_func_array([$this->preset_output, $method], $params);
    }

    public function testStart()
    {
        $this->progress
            ->expects($this->once())
            ->method('start')
            ->with($this->output, 123);
        $this->preset_output->start(123);
    }
}
