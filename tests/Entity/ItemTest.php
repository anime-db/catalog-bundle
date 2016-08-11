<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Tests\Entity;

use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use Symfony\Component\Validator\ExecutionContextInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Test item.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    protected $item;

    protected function setUp()
    {
        $this->item = new Item();
    }

    public function testDoChangeDateUpdate()
    {
        $date = (new \DateTime())->modify('+100 seconds');
        $this->item->setDateUpdate($date);

        $this->item->doChangeDateUpdate();
        $this->assertInstanceOf('\DateTime', $this->item->getDateUpdate());
        $this->assertNotEquals($date, $this->item->getDateUpdate());
    }

    /**
     * @return array
     */
    public function getRequiredPaths()
    {
        return [
            [false, false, ''],
            [true, false, ''],
            [true, true, ''],
            [true, true, 'foo'],
        ];
    }

    /**
     * @dataProvider getRequiredPaths
     *
     * @param bool $storage
     * @param bool $required
     * @param string $path
     */
    public function testIsPathValid($storage, $required, $path)
    {
        if ($storage) {
            $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
            $storage
                ->expects($this->once())
                ->method('isPathRequired')
                ->will($this->returnValue($required));
            $this->item->setStorage($storage);
        }
        /* @var $context \PHPUnit_Framework_MockObject_MockObject|ExecutionContextInterface */
        $context = $this->getMock('\Symfony\Component\Validator\ExecutionContextInterface');
        $context
            ->expects($storage && $required && !$path ? $this->once() : $this->never())
            ->method('addViolationAt')
            ->with('path', 'Path is required to fill for current type of storage');
        $this->item->setPath($path);
        $this->item->isPathValid($context);
    }

    public function testFreez()
    {
        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $em
            ->expects($this->atLeastOnce())
            ->method('getReference')
            ->will($this->returnCallback(function ($class_name, $id) {
                $ref = new \stdClass();
                $ref->class = $class_name;
                $ref->id = $id;

                return $ref;
            }));
        /* @var $doctrine \PHPUnit_Framework_MockObject_MockObject|Registry */
        $doctrine = $this->getMockBuilder('\Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $doctrine
            ->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($em));

        // set related entities
        $country = $this->getRef('\AnimeDb\Bundle\CatalogBundle\Entity\Country', 'setCountry');
        $storage = $this->getRef('\AnimeDb\Bundle\CatalogBundle\Entity\Storage', 'setStorage');
        $type = $this->getRef('\AnimeDb\Bundle\CatalogBundle\Entity\Type', 'setType');
        $genre1 = $this->getRef('\AnimeDb\Bundle\CatalogBundle\Entity\Genre', 'addGenre');
        $genre2 = $this->getRef('\AnimeDb\Bundle\CatalogBundle\Entity\Genre', 'addGenre');

        $this->item->freez($doctrine);

        // test freez result
        $this->assertEquals($country, $this->item->getCountry());
        $this->assertEquals($storage, $this->item->getStorage());
        $this->assertEquals($type, $this->item->getType());
        $this->assertEquals($genre1, $this->item->getGenres()[0]);
        $this->assertEquals($genre2, $this->item->getGenres()[1]);
    }

    /**
     * Get reference.
     *
     * @param string $entity
     * @param string $set
     *
     * @return \stdClass
     */
    protected function getRef($entity, $set)
    {
        $mock = $this->getMock($entity);
        $mock
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($id = rand()));
        call_user_func([$this->item, $set], $mock);

        $ref = new \stdClass();
        $ref->class = get_class($mock);
        $ref->id = $id;

        return $ref;
    }

    /**
     * @return array
     */
    public function getClearedPaths()
    {
        return [
            ['', '', ''],
            ['foo', '', ''],
            ['foo/bar', 'baz', ''],
            ['foo/bar', 'foo', '/bar'],
        ];
    }

    /**
     * @dataProvider getClearedPaths
     *
     * @param string $path
     * @param string $storage_path
     * @param string $cleared_path
     */
    public function testDoClearPath($path, $storage_path, $cleared_path)
    {
        if ($storage_path) {
            $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
            $storage
                ->expects($this->atLeastOnce())
                ->method('getPath')
                ->will($this->returnValue($storage_path));
            $this->item->setStorage($storage);
        }
        $this->item->setPath($path);
        $this->assertEquals($cleared_path, $this->item->getRealPath());
        $this->assertEquals($path, $this->item->getPath());
    }

    /**
     * @return array
     */
    public function getUrlNames()
    {
        return [
            ['foo', 'foo'],
            ['foo   bar', 'foo_bar'],
            ['foo bar: 1', 'foo_bar:_1'],
        ];
    }

    /**
     * @dataProvider getUrlNames
     *
     * @param string $name
     * @param string $expected
     */
    public function testGetUrlName($name, $expected)
    {
        $this->item->setName($name);
        $this->assertEquals($expected, $this->item->getUrlName());
    }
}
