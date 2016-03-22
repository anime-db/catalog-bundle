<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Event\Storage;

use AnimeDb\Bundle\CatalogBundle\Event\Storage\DetectedNewFiles;

/**
 * Test event DetectedNewFiles
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Event\Storage
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class DetectedNewFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Storage
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storage;

    /**
     * SplFileInfo
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $file;

    protected function setUp()
    {
        touch(sys_get_temp_dir().'/test');
        $this->storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $this->file = $this->getMockBuilder('\Symfony\Component\Finder\SplFileInfo')
            ->setConstructorArgs([sys_get_temp_dir().'/test', '', ''])
            ->getMock();
    }

    protected function tearDown()
    {
        unlink(sys_get_temp_dir().'/test');
    }

    /**
     * Test get storage
     */
    public function testGetStorage()
    {
        $this->assertEquals($this->storage, $this->getEvent()->getStorage());
    }

    /**
     * Test get file
     */
    public function testGetFile()
    {
        $this->assertEquals($this->file, $this->getEvent()->getFile());
    }

    /**
     * Get filenames
     *
     * @return array
     */
    public function getFilenames()
    {
        return [
            ['test', 'test.log', true],
            ['test', 'test', false],
            ['test', 'test [123].log', true],
            ['test', 'test [123]', false],
            ['test', 'test (123).log', true],
            ['test', 'test (123)', false],
            ['test', ' test ()[].log', true],
            ['test', ' test ()[]', false]
        ];
    }

    /**
     * Test get name
     *
     * @dataProvider getFilenames
     *
     * @param string $expected
     * @param string $filename
     * @param bool $is_file
     */
    public function testGetName($expected, $filename, $is_file)
    {
        $this->file
            ->expects($this->once())
            ->method('getFilename')
            ->will($this->returnValue($filename));
        $this->file
            ->expects($this->once())
            ->method('isFile')
            ->will($this->returnValue($is_file));

        $this->assertEquals($expected, $this->getEvent()->getName());
    }

    /**
     * Get event
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Event\Storage\DetectedNewFiles
     */
    protected function getEvent()
    {
        return new DetectedNewFiles($this->storage, $this->file);
    }
}
