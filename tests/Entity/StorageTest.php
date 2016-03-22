<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Entity;

use AnimeDb\Bundle\CatalogBundle\Entity\Storage;

/**
 * Test storage
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Storage
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Storage
     */
    protected $storage;

    protected function setUp()
    {
        $this->storage = new Storage();
    }

    /**
     * Test get types
     */
    public function testGetTypes()
    {
        $this->assertEquals([
            Storage::TYPE_FOLDER,
            Storage::TYPE_EXTERNAL,
            Storage::TYPE_EXTERNAL_R,
            Storage::TYPE_VIDEO
        ], Storage::getTypes());
    }

    /**
     * Test get type titles
     */
    public function testGetTypeTitles()
    {
        $this->assertEquals([
            Storage::TYPE_FOLDER => 'Folder on computer (local/network)',
            Storage::TYPE_EXTERNAL => 'External storage (HDD/Flash/SD)',
            Storage::TYPE_EXTERNAL_R => 'External storage read-only (CD/DVD)',
            Storage::TYPE_VIDEO => 'Video storage (DVD/BD/VHS)'
        ], Storage::getTypeTitles());
    }

    /**
     * Test get types writable
     */
    public function testGetTypesWritable()
    {
        $this->assertEquals([
            Storage::TYPE_FOLDER,
            Storage::TYPE_EXTERNAL
        ], Storage::getTypesWritable());
    }

    /**
     * Test get types readable
     */
    public function testGetTypesReadable()
    {
        $this->assertEquals([
            Storage::TYPE_FOLDER,
            Storage::TYPE_EXTERNAL,
            Storage::TYPE_EXTERNAL_R
        ], Storage::getTypesReadable());
    }

    /**
     * Get types
     *
     * @return array
     */
    public function getTypes()
    {
        return [
            [''],
            [Storage::TYPE_FOLDER],
            [Storage::TYPE_EXTERNAL],
            [Storage::TYPE_EXTERNAL_R],
            [Storage::TYPE_VIDEO]
        ];
    }

    /**
     * Test get type title
     *
     * @dataProvider getTypes
     *
     * @param string $type
     */
    public function testTypeTitle($type)
    {
        $this->storage->setType($type);
        if ($type) {
            $titles = Storage::getTypeTitles();
            $this->assertEquals($titles[$type], $this->storage->getTypeTitle());
        } else {
            $this->assertEmpty($this->storage->getTypeTitle());
        }
    }

    /**
     * Get access
     *
     * @return array
     */
    public function getAccess()
    {
        $params = [];
        foreach ($this->getTypes() as $type) {
            $params[] = ['isWritable', Storage::getTypesWritable(), $type[0]];
        }
        foreach ($this->getTypes() as $type) {
            $params[] = ['isPathRequired', Storage::getTypesWritable(), $type[0]];
        }
        foreach ($this->getTypes() as $type) {
            $params[] = ['isReadable', Storage::getTypesReadable(), $type[0]];
        }
        return $params;
    }

    /**
     * Test access
     *
     * @dataProvider getAccess
     *
     * @param string $method
     * @param array $expected
     * @param string $type
     */
    public function testAccess($method, array $expected, $type)
    {
        $this->storage->setType($type);
        $this->assertEquals(in_array($type, $expected), call_user_func([$this->storage, $method]));
    }

    /**
     * Get required paths
     *
     * @return array
     */
    public function getRequiredPaths()
    {
        $params = [];
        foreach ($this->getTypes() as $type) {
            $params[] = [$type[0], ''];
        }
        foreach ($this->getTypes() as $type) {
            $params[] = [$type[0], 'foo'];
        }
        return $params;
    }

    /**
     * Test is path valid
     *
     * @dataProvider getRequiredPaths
     *
     * @param string $type
     * @param bool $required
     * @param string $path
     */
    public function testIsPathValid($type, $path)
    {
        $this->storage->setType($type);
        $this->storage->setPath($path);
        $context = $this->getMock('\Symfony\Component\Validator\ExecutionContextInterface');
        $context
            ->expects($this->storage->isPathRequired() && !$path ? $this->once() : $this->never())
            ->method('addViolationAt')
            ->with('path', 'Path is required to fill for current type of storage');
        $this->storage->isPathValid($context);
    }

    /**
     * Test do change date update
     */
    public function testDoChangeDateUpdate()
    {
        $date = (new \DateTime())->modify('+100 seconds');
        $this->storage->setDateUpdate($date);

        $this->storage->doChangeDateUpdate();
        $this->assertInstanceOf('\DateTime', $this->storage->getDateUpdate());
        $this->assertNotEquals($date, $this->storage->getDateUpdate());
    }

    /**
     * Test get old paths
     */
    public function testGetOldPaths()
    {
        $this->assertEmpty($this->storage->getOldPaths());

        $this->storage->setPath('foo');
        $this->assertEmpty($this->storage->getOldPaths());

        $this->storage->setPath('bar');
        $this->assertEquals(['foo'], $this->storage->getOldPaths());

        $this->storage->setPath('baz');
        $this->assertEquals(['foo', 'bar'], $this->storage->getOldPaths());
    }
}
