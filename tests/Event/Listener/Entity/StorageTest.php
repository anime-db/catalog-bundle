<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Event\Listener\Entity;

use AnimeDb\Bundle\CatalogBundle\Event\Listener\Entity\Storage;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Test storage entity listener.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Root dir.
     *
     * @var string
     */
    protected $root;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Filesystem
     */
    protected $fs;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $real_fs;

    /**
     * @var \AnimeDb\Bundle\CatalogBundle\Event\Listener\Entity\Storage
     */
    protected $storage;

    protected function setUp()
    {
        $this->root = sys_get_temp_dir().'/test/';
        $this->fs = $this->getMock('\Symfony\Component\Filesystem\Filesystem');
        $this->storage = new Storage($this->fs);
        $this->real_fs = new Filesystem();
    }

    protected function tearDown()
    {
        $this->real_fs->remove($this->root);
    }

    /**
     * @return array
     */
    public function getEventListeners()
    {
        return [
            ['postPersist'],
            ['postRemove'],
            ['postUpdate'],
        ];
    }

    /**
     * @dataProvider getEventListeners
     *
     * @param string $method
     */
    public function testNoStorage($method)
    {
        $this->fs
            ->expects($this->never())
            ->method('exists');
        call_user_func([$this->storage, $method], $this->getArgs(new \stdClass()));
    }

    public function testPostPersistNoPath()
    {
        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $storage
            ->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue('foo'));
        $this->fs
            ->expects($this->at(0))
            ->method('exists')
            ->will($this->returnValue(false))
            ->with('foo');
        $this->fs
            ->expects($this->never())
            ->method('dumpFile');
        $this->storage->postPersist($this->getArgs($storage));
    }

    public function testPostPersistFileExists()
    {
        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $storage
            ->expects($this->atLeastOnce())
            ->method('getPath')
            ->will($this->returnValue('foo'));
        $this->fs
            ->expects($this->at(0))
            ->method('exists')
            ->will($this->returnValue(true))
            ->with('foo');
        $this->fs
            ->expects($this->at(1))
            ->method('exists')
            ->will($this->returnValue(true))
            ->with('foo'.Storage::ID_FILE);
        $this->fs
            ->expects($this->never())
            ->method('dumpFile');
        $this->storage->postPersist($this->getArgs($storage));
    }

    public function testPostPersist()
    {
        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $storage
            ->expects($this->atLeastOnce())
            ->method('getPath')
            ->will($this->returnValue('foo'));
        $storage
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->will($this->returnValue(123));
        $this->fs
            ->expects($this->at(0))
            ->method('exists')
            ->will($this->returnValue(true))
            ->with('foo');
        $this->fs
            ->expects($this->at(1))
            ->method('exists')
            ->will($this->returnValue(false))
            ->with('foo'.Storage::ID_FILE);
        $this->fs
            ->expects($this->once())
            ->method('dumpFile')
            ->with('foo'.Storage::ID_FILE, 123, 0666);
        $this->storage->postPersist($this->getArgs($storage));
    }

    public function testPostRemoveNoPath()
    {
        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $storage
            ->expects($this->atLeastOnce())
            ->method('getPath')
            ->will($this->returnValue('foo'));
        $this->fs
            ->expects($this->at(0))
            ->method('exists')
            ->will($this->returnValue(false))
            ->with('foo'.Storage::ID_FILE);
        $this->fs
            ->expects($this->never())
            ->method('remove');
        $this->storage->postRemove($this->getArgs($storage));
    }

    public function testPostRemoveBadFile()
    {
        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $storage
            ->expects($this->atLeastOnce())
            ->method('getPath')
            ->will($this->returnValue($this->root));
        $storage
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(123));
        $this->fs
            ->expects($this->at(0))
            ->method('exists')
            ->will($this->returnValue(true))
            ->with($this->root.Storage::ID_FILE);
        $this->fs
            ->expects($this->never())
            ->method('remove');

        $this->real_fs->dumpFile($this->root.Storage::ID_FILE, 456);

        $this->storage->postRemove($this->getArgs($storage));
    }

    public function testPostRemove()
    {
        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $storage
            ->expects($this->atLeastOnce())
            ->method('getPath')
            ->will($this->returnValue($this->root));
        $storage
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(123));
        $this->fs
            ->expects($this->at(0))
            ->method('exists')
            ->will($this->returnValue(true))
            ->with($this->root.Storage::ID_FILE);
        $this->fs
            ->expects($this->once())
            ->method('remove')
            ->with($this->root.Storage::ID_FILE);

        $this->real_fs->dumpFile($this->root.Storage::ID_FILE, 123);

        $this->storage->postRemove($this->getArgs($storage));
    }

    /**
     * @param object $storage
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|LifecycleEventArgs
     */
    protected function getArgs($storage)
    {
        $args = $this->getMockBuilder('\Doctrine\ORM\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock();
        $args
            ->expects($this->atLeastOnce())
            ->method('getEntity')
            ->will($this->returnValue($storage));

        return $args;
    }

    public function testPostUpdateNoFile()
    {
        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $storage
            ->expects($this->atLeastOnce())
            ->method('getPath')
            ->will($this->returnValue('baz'));
        $storage
            ->expects($this->once())
            ->method('getOldPaths')
            ->will($this->returnValue(['foo', 'bar']));
        $this->fs
            ->expects($this->at(0))
            ->method('exists')
            ->will($this->returnValue(false))
            ->with('foo'.Storage::ID_FILE);
        $this->fs
            ->expects($this->at(1))
            ->method('exists')
            ->will($this->returnValue(false))
            ->with('bar'.Storage::ID_FILE);
        $this->fs
            ->expects($this->at(2))
            ->method('exists')
            ->will($this->returnValue(false))
            ->with('baz');
        $this->fs
            ->expects($this->never())
            ->method('remove');

        $this->storage->postUpdate($this->getArgs($storage));
    }

    public function testPostUpdateBadId()
    {
        $file1 = $this->root.'foo/'.Storage::ID_FILE;
        $file2 = $this->root.'bar/'.Storage::ID_FILE;

        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $storage
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->will($this->returnValue(123));
        $storage
            ->expects($this->atLeastOnce())
            ->method('getPath')
            ->will($this->returnValue('baz'));
        $storage
            ->expects($this->once())
            ->method('getOldPaths')
            ->will($this->returnValue([dirname($file1).'/', dirname($file2).'/']));
        $this->fs
            ->expects($this->atLeastOnce())
            ->method('exists')
            ->will($this->returnValue(true));
        $this->fs
            ->expects($this->never())
            ->method('remove');

        $this->real_fs->dumpFile($file1, 456);
        $this->real_fs->dumpFile($file2, 456);

        $this->storage->postUpdate($this->getArgs($storage));
    }

    public function testPostUpdate()
    {
        $file1 = $this->root.'foo/'.Storage::ID_FILE;
        $file2 = $this->root.'bar/'.Storage::ID_FILE;

        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $storage
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->will($this->returnValue(123));
        $storage
            ->expects($this->atLeastOnce())
            ->method('getPath')
            ->will($this->returnValue('baz'));
        $storage
            ->expects($this->once())
            ->method('getOldPaths')
            ->will($this->returnValue([dirname($file1).'/', dirname($file2).'/']));
        $this->fs
            ->expects($this->at(0))
            ->method('exists')
            ->will($this->returnValue(true))
            ->with($file1);
        $this->fs
            ->expects($this->at(2))
            ->method('exists')
            ->will($this->returnValue(true))
            ->with($file2);
        $this->fs
            ->expects($this->at(4))
            ->method('exists')
            ->will($this->returnValue(true))
            ->with('baz');
        $this->fs
            ->expects($this->at(5))
            ->method('exists')
            ->will($this->returnValue(false))
            ->with('baz'.Storage::ID_FILE);
        $this->fs
            ->expects($this->at(1))
            ->method('remove')
            ->with($file1);
        $this->fs
            ->expects($this->at(3))
            ->method('remove')
            ->with($file2);
        $this->fs
            ->expects($this->once())
            ->method('dumpFile')
            ->with('baz'.Storage::ID_FILE, 123, 0666);

        $this->real_fs->dumpFile($file1, 123);
        $this->real_fs->dumpFile($file2, 123);

        $this->storage->postUpdate($this->getArgs($storage));
    }
}
