<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Service\Item\Search;

use AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Manager;

/**
 * Test search manager
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Service\Item\Search
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Search manager
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Manager
     */
    protected $manager;

    /**
     * Search driver
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $driver;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->driver = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Service\Item\Search\DriverInterface');
        $this->manager = new Manager($this->driver);
    }

    /**
     * Get search data
     *
     * @return array
     */
    public function getSearchData()
    {
        return [
            [10, 20, 'name', 'ASC'],
            [-10, -20, 'date_update', 'DESC'],
            [10, -20, 'undefined', 'undefined']
        ];
    }

    /**
     * Test search
     *
     * @dataProvider getSearchData
     *
     * @param integer $limit
     * @param integer $offset
     * @param string $sort_column
     * @param string $sort_direction
     */
    public function testSearch($limit, $offset, $sort_column, $sort_direction)
    {
        $data = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Search');
        $expected = ['foo', 'bar'];
        $this->driver
            ->expects($this->once())
            ->method('search')
            ->willReturn($expected)
            ->with(
                $data,
                $limit > 0 ? (int)$limit : 0,
                $offset > 0 ? (int)$offset : 0,
                $this->manager->getValidSortColumn($sort_column),
                $this->manager->getValidSortDirection($sort_direction)
            );
        $this->assertEquals($expected, $this->manager->search($data, $limit, $offset, $sort_column, $sort_direction));
    }

    /**
     * Test search by name
     */
    public function testSearchByName()
    {
        $expected = ['foo', 'bar'];
        $this->driver
            ->expects($this->once())
            ->method('searchByName')
            ->willReturn($expected)
            ->with('my_name', 123);
        $this->assertEquals($expected, $this->manager->searchByName('my_name', 123));
    }

    /**
     * Get valid sort methods
     *
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
     * Test get valid sort column
     *
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
