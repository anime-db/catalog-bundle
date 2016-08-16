<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Tests\Event\Storage;

use AnimeDb\Bundle\CatalogBundle\Event\Storage\DetectedNewFiles;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Test event DetectedNewFiles.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class DetectedNewFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Storage
     */
    protected $storage;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|SplFileInfo
     */
    protected $file;

    /**
     * @var string
     */
    protected $name;

    protected function setUp()
    {
        touch(sys_get_temp_dir().'/test');
        $this->name = 'foo';
        $this->storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $this->file = $this
            ->getMockBuilder('\Symfony\Component\Finder\SplFileInfo')
            ->setConstructorArgs([sys_get_temp_dir().'/test', '', ''])
            ->getMock();
    }

    protected function tearDown()
    {
        unlink(sys_get_temp_dir().'/test');
    }

    public function testGetStorage()
    {
        $this->assertEquals($this->storage, $this->getEvent()->getStorage());
    }

    public function testGetFile()
    {
        $this->assertEquals($this->file, $this->getEvent()->getFile());
    }

    public function testGetName()
    {
        $this->assertEquals($this->name, $this->getEvent()->getName());
    }

    /**
     * @return \AnimeDb\Bundle\CatalogBundle\Event\Storage\DetectedNewFiles
     */
    protected function getEvent()
    {
        return new DetectedNewFiles($this->storage, $this->file, $this->name);
    }
}
