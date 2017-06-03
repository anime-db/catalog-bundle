<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Service\Item\Search;

use AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Manager;
use AnimeDb\Bundle\CatalogBundle\Service\Item\Search\DriverInterface;
use AnimeDb\Bundle\CatalogBundle\Entity\Search;

/**
 * Test search manager.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Manager
     */
    protected $manager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|DriverInterface
     */
    protected $driver;

    protected function setUp()
    {
        $this->driver = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Service\Item\Search\DriverInterface');
        $this->manager = new Manager($this->driver);
    }

    /**
     * @return array
     */
    public function getSearchData()
    {
        return [
            [10, 20, 'name', 'ASC'],
            [-10, -20, 'date_update', 'DESC'],
            [10, -20, 'undefined', 'undefined'],
        ];
    }

    /**
     * @dataProvider getSearchData
     *
     * @param int $limit
     * @param int $offset
     * @param string $sort_column
     * @param string $sort_direction
     */
    public function testSearch($limit, $offset, $sort_column, $sort_direction)
    {
        /* @var $data \PHPUnit_Framework_MockObject_MockObject|Search */
        $data = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Search');
        $expected = ['foo', 'bar'];
        $this->driver
            ->expects($this->once())
            ->method('search')
            ->will($this->returnValue($expected))
            ->with(
                $data,
                $limit > 0 ? (int) $limit : 0,
                $offset > 0 ? (int) $offset : 0,
                $this->manager->getValidSortColumn($sort_column),
                $this->manager->getValidSortDirection($sort_direction)
            );
        $this->assertEquals($expected, $this->manager->search($data, $limit, $offset, $sort_column, $sort_direction));
    }

    public function testSearchByName()
    {
        $expected = ['foo', 'bar'];
        $this->driver
            ->expects($this->once())
            ->method('searchByName')
            ->will($this->returnValue($expected))
            ->with('my_name', 123);
        $this->assertEquals($expected, $this->manager->searchByName('my_name', 123));
    }

    /**
     * @return array
     */
    public function getValidSortMethods()
    {
        return [
            ['getValidSortColumn', 'name', 'name'],
            ['getValidSortColumn', 'date_update', 'date_update'],
            ['getValidSortColumn', 'rating', 'rating'],
            ['getValidSortColumn', 'date_premiere', 'date_premiere'],
            ['getValidSortColumn', 'date_end', 'date_end'],
            ['getValidSortColumn', 'undefined', Manager::DEFAULT_SORT_COLUMN],
            ['getValidSortDirection', 'ASC', 'ASC'],
            ['getValidSortDirection', 'DESC', 'DESC'],
            ['getValidSortDirection', 'undefined', Manager::DEFAULT_SORT_DIRECTION],
        ];
    }

    /**
     * @dataProvider getValidSortMethods
     *
     * @param string $method
     * @param string $column
     * @param string $expected
     */
    public function testGetValidSort($method, $column, $expected)
    {
        $this->assertEquals($expected, call_user_func([$this->manager, $method], $column));
    }
}
