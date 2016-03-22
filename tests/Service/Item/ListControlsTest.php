<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Service\Item;

use AnimeDb\Bundle\CatalogBundle\Service\Item\ListControls;
use AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Manager;

/**
 * Test list controls
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Service\Item
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ListControlsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Manager
     */
    protected $searcher;

    /**
     * @var \AnimeDb\Bundle\CatalogBundle\Service\Item\ListControls
     */
    protected $controls;

    public function setUp()
    {
        $this->searcher = $this
            ->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Manager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->controls = new ListControls($this->searcher);
    }

    /**
     * @return array
     */
    public function getLimits()
    {
        return [
            [['my_key' => 'my_var', 'limit' => 8], 8],
            [['my_key' => 'my_var', 'limit' => 16], 16],
            [['my_key' => 'my_var', 'limit' => 32], 32],
            [['my_key' => 'my_var', 'limit' => ListControls::LIMIT_ALL], ListControls::LIMIT_ALL],
            [['my_key' => 'my_var', 'limit' => 100], ListControls::DEFAULT_LIMIT],
            [['my_key' => 'my_var', 'limit' => 'undefined'], ListControls::DEFAULT_LIMIT],
            [['my_key' => 'my_var'], ListControls::DEFAULT_LIMIT]
        ];
    }

    /**
     * @dataProvider getLimits
     *
     * @param array $query
     * @param int $expected
     */
    public function testGetLimit(array $query, $expected)
    {
        $this->assertEquals($expected, $this->controls->getLimit($query));
    }

    /**
     * @dataProvider getLimits
     *
     * @param array $query
     * @param int $expected
     */
    public function testGetLimits(array $query, $expected)
    {
        $limits = [];
        foreach (ListControls::$limits as $limit) {
            $limits[] = [
                'link' => '?'.http_build_query(array_merge($query, ['limit' => $limit])),
                'name' => $limit ? $limit : ListControls::LIMIT_ALL_NAME,
                'count' => $limit,
                'current' => $expected == $limit
            ];
        }
        $this->assertEquals($limits, $this->controls->getLimits($query));
    }

    /**
     * @return array
     */
    public function getSortColumns()
    {
        return [
            [['my_key' => 'my_var', 'sort_by' => 'my_field'], 'my_field'],
            [['my_key' => 'my_var'], 'undefined'],
        ];
    }

    /**
     * @dataProvider getSortColumns
     *
     * @param array $query
     * @param string $expected
     */
    public function testGetSortColumn(array $query, $expected)
    {
        $this->searcher
            ->expects($this->once())
            ->method('getValidSortColumn')
            ->will($this->returnValue($expected))
            ->with(isset($query['sort_by']) ? $query['sort_by'] : null);

        $this->assertEquals($expected, $this->controls->getSortColumn($query));
    }

    /**
     * @dataProvider getSortColumns
     *
     * @param array $query
     * @param string $expected
     */
    public function testGetSortColumns(array $query, $expected)
    {
        $this->searcher
            ->expects($this->once())
            ->method('getValidSortColumn')
            ->will($this->returnValue($expected))
            ->with(isset($query['sort_by']) ? $query['sort_by'] : null);

        $sort_by = [];
        foreach (ListControls::$sort_by_column as $column => $info) {
            $sort_by[] = [
                'name' => $info['name'],
                'title' => $info['title'],
                'current' => $expected == $column,
                'link' => '?'.http_build_query(
                    array_merge($query, ['sort_by' => $column])
                )
            ];
        }

        $this->assertEquals($sort_by, $this->controls->getSortColumns($query));
    }

    /**
     * @return array
     */
    public function getSortDirections()
    {
        return [
            [['my_key' => 'my_var', 'sort_direction' => 'ASC'], 'ASC'],
            [['my_key' => 'my_var', 'sort_direction' => 'DESC'], 'DESC'],
            [['my_key' => 'my_var'], 'undefined'],
        ];
    }

    /**
     * @dataProvider getSortDirections
     *
     * @param array $query
     * @param string $expected
     */
    public function testGetSortDirection(array $query, $expected)
    {
        $this->searcher
            ->expects($this->once())
            ->method('getValidSortDirection')
            ->will($this->returnValue($expected))
            ->with(isset($query['sort_direction']) ? $query['sort_direction'] : null);

        $this->assertEquals($expected, $this->controls->getSortDirection($query));
    }

    /**
     * @dataProvider getSortDirections
     *
     * @param array $query
     * @param string $expected
     */
    public function testGetSortDirectionLink(array $query, $expected)
    {
        $this->searcher
            ->expects($this->once())
            ->method('getValidSortDirection')
            ->will($this->returnValue($expected))
            ->with(isset($query['sort_direction']) ? $query['sort_direction'] : null);

        $this->assertEquals('?'.http_build_query(
            array_merge($query, ['sort_direction' => $expected == 'ASC' ? 'DESC' : 'ASC'])
        ), $this->controls->getSortDirectionLink($query));
    }
}
