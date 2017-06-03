<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Service\Item\Search\Driver;

use AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Driver\SqlLike;
use Doctrine\Bundle\DoctrineBundle\Registry;
use AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector;
use AnimeDb\Bundle\CatalogBundle\Entity\Search;

/**
 * Test SqlLike.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class SqlLikeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Driver\SqlLike
     */
    protected $driver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Selector
     */
    protected $selector;

    protected function setUp()
    {
        $driver = $this->getMock('\Doctrine\DBAL\Driver\Connection');
        $conn = $this
            ->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $conn
            ->expects($this->once())
            ->method('getWrappedConnection')
            ->will($this->returnValue($driver));
        $this->repository = $this
            ->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Repository\Item')
            ->disableOriginalConstructor()
            ->getMock();
        /* @var $doctrine \PHPUnit_Framework_MockObject_MockObject|Registry */
        $doctrine = $this
            ->getMockBuilder('\Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $doctrine
            ->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($this->repository))
            ->with('AnimeDbCatalogBundle:Item');
        $doctrine
            ->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($conn));
        $this->selector = $this
            ->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector')
            ->disableOriginalConstructor()
            ->getMock();
        $this->driver = new SqlLike($doctrine, $this->selector);
    }

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
            ->will($this->returnValue($this->getQuery($result['list'])));
        $total
            ->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($this->getQuery($result['total'], true)));

        /* @var $entity \PHPUnit_Framework_MockObject_MockObject|Search */
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
            'addType',
        ];
        foreach ($methods as $method) {
            $builder
                ->expects($this->once())
                ->method($method)
                ->with($entity)
                ->will($this->returnSelf());
        }
        $builder
            ->expects($this->once())
            ->method('sort')
            ->with('my_column', 'my_direction')
            ->will($this->returnSelf());
        $builder
            ->expects($this->once())
            ->method('limit')
            ->with(111)
            ->will($this->returnSelf());
        $builder
            ->expects($this->once())
            ->method('offset')
            ->with(222)
            ->will($this->returnSelf());
        $builder
            ->expects($this->once())
            ->method('getQuerySelect')
            ->will($this->returnValue($select));
        $builder
            ->expects($this->once())
            ->method('getQueryTotal')
            ->will($this->returnValue($total));
        $this->selector
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($builder));
        // TODO add mocks
        $this->assertEquals($result, $this->driver->search($entity, 111, 222, 'my_column', 'my_direction'));
    }

    public function testSearchByNameFail()
    {
        $this->repository
            ->expects($this->never())
            ->method('createQueryBuilder');
        $this->assertEquals([], $this->driver->searchByName(''));
    }

    /**
     * @return array
     */
    public function getNames()
    {
        return [
            ['foo', 'foo%', -1],
            ['BAR', 'bar%', 0],
            ['ПрИвЕт', 'привет%', 1],
            ['foo%', 'foo%%%', 1],
            ['foo%%', 'foo%%%', 1],
        ];
    }

    /**
     * @dataProvider getNames
     *
     * @param string $name
     * @param string $expected
     * @param int $limit
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
            ->with('i.names', 'n')
            ->will($this->returnSelf());
        $builder
            ->expects($this->once())
            ->method('where')
            ->with('LOWER(i.name) LIKE :name')
            ->will($this->returnSelf());
        $builder
            ->expects($this->once())
            ->method('orWhere')
            ->with('LOWER(n.name) LIKE :name')
            ->will($this->returnSelf());
        $builder
            ->expects($this->once())
            ->method('setParameter')
            ->with('name', $expected)
            ->will($this->returnSelf());
        $builder
            ->expects($this->once())
            ->method('groupBy')
            ->with('i')
            ->will($this->returnSelf());
        if ($limit > 0) {
            $builder
                ->expects($this->once())
                ->method('setMaxResults')
                ->with($limit)
                ->will($this->returnSelf());
        }
        $builder
            ->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($this->getQuery($result)));
        $this->repository
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->will($this->returnValue($builder))
            ->with('i');
        $this->assertEquals($result, $this->driver->searchByName($name, $limit));
    }

    /**
     * @param mixed $result
     * @param bool $single
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
            ->will($this->returnValue($result));

        return $query;
    }
}
