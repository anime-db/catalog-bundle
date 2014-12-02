<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Service\Item\Search\Driver;

use AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Driver\SqlLike;

/**
 * Test SqlLike
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Service\Item\Search\Driver\SqlLike
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class SqlLikeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Driver
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Driver\SqlLike
     */
    protected $driver;

    /**
     * Repository
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repository;

    /**
     * Selector
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $selector;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $driver = $this->getMock('\Doctrine\DBAL\Driver\Connection');
        $conn = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $conn
            ->expects($this->once())
            ->method('getWrappedConnection')
            ->willReturn($driver);
        $this->repository = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Repository\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $doctrine = $this->getMockBuilder('\Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $doctrine
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->repository)
            ->with('AnimeDbCatalogBundle:Item');
        $doctrine
            ->expects($this->once())
            ->method('getConnection')
            ->willReturn($conn);
        $this->selector = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector')
            ->disableOriginalConstructor()
            ->getMock();
        $this->driver = new SqlLike($doctrine, $this->selector);
    }

    /**
     * Test search
     */
    public function testSearch()
    {
        $result = ['list' => ['foo', 'bar'], 'total' => 123];
        // exec query
        $select = $this->getMockBuilder('\Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $total = $this->getMockBuilder('\Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $select
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($this->getQuery($result['list']));
        $total
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($this->getQuery($result['total'], true));

        $entity = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Search');
        // build query selector
        $builder = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector\Builder')
            ->disableOriginalConstructor()
            ->getMock();
        $methods = [
            'addCountry',
            'addDateAdd',
            'addDateEnd',
            'addDatePremiere',
            'addGenres',
            'addLabels',
            'addName',
            'addStorage',
            'addStudio',
            'addType'
        ];
        foreach ($methods as $method) {
            $builder
                ->expects($this->once())
                ->method($method)
                ->willReturnSelf()
                ->with($entity);
        }
        $builder
            ->expects($this->once())
            ->method('sort')
            ->willReturnSelf()
            ->with('my_column', 'my_direction');
        $builder
            ->expects($this->once())
            ->method('limit')
            ->willReturnSelf()
            ->with(111);
        $builder
            ->expects($this->once())
            ->method('offset')
            ->willReturnSelf()
            ->with(222);
        $builder
            ->expects($this->once())
            ->method('getQuerySelect')
            ->willReturn($select);
        $builder
            ->expects($this->once())
            ->method('getQueryTotal')
            ->willReturn($total);
        $this->selector
            ->expects($this->once())
            ->method('create')
            ->willReturn($builder);
        // TODO add mocks
        $this->assertEquals($result, $this->driver->search($entity, 111, 222, 'my_column', 'my_direction'));
    }

    /**
     * Test search by name fail
     */
    public function testSearchByNameFail()
    {
        $this->repository
            ->expects($this->never())
            ->method('createQueryBuilder');
        $this->assertEquals([], $this->driver->searchByName(''));
    }

    /**
     * Get names
     *
     * @return array
     */
    public function getNames()
    {
        return [
            ['foo', 'foo%', -1],
            ['BAR', 'bar%', 0],
            ['ПрИвЕт', 'привет%', 1],
            ['foo%', 'foo%%%', 1],
            ['foo%%', 'foo%%%', 1]
        ];
    }

    /**
     * Test search by name
     *
     * @dataProvider getNames
     *
     * @param string $name
     * @param string $expected
     * @param integer $limit
     */
    public function testSearchByName($name, $expected, $limit)
    {
        $result = ['foo', 'bar'];
        $builder = $this->getMockBuilder('\Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $builder
            ->expects($this->once())
            ->method('innerJoin')
            ->willReturnSelf()
            ->with('i.names', 'n');
        $builder
            ->expects($this->once())
            ->method('andWhere')
            ->willReturnSelf()
            ->with('LOWER(i.name) LIKE :name OR LOWER(n.name) LIKE :name');
        $builder
            ->expects($this->once())
            ->method('setParameter')
            ->willReturnSelf()
            ->with('name', $expected);
        $builder
            ->expects($this->once())
            ->method('groupBy')
            ->willReturnSelf()
            ->with('i');
        if ($limit > 0) {
            $builder
                ->expects($this->once())
                ->method('setMaxResults')
                ->willReturnSelf()
                ->with($limit);
        }
        $builder
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($this->getQuery($result));
        $this->repository
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($builder)
            ->with('i');
        $this->assertEquals($result, $this->driver->searchByName($name, $limit));
    }

    /**
     * Get query
     *
     * @param mixed $result
     * @param boolean $single
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getQuery($result, $single = false)
    {
        $query = $this->getMockBuilder('\Doctrine\ORM\AbstractQuery')
            ->setMethods(['getSingleScalarResult', 'getResult'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $query
            ->expects($this->once())
            ->method($single ? 'getSingleScalarResult' : 'getResult')
            ->willReturn($result);
        return $query;
    }
}
