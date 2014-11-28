<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Service\Item\Search\Selector;

use AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector\Builder;

/**
 * Test selector builder
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Service\Item\Search\Selector
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Select query builder
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $select;

    /**
     * Total query builder
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $total;

    /**
     * Builder
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector\Builder
     */
    protected $builder;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->select = $this->getMockBuilder('\Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->select
            ->expects($this->once())
            ->method('groupBy')
            ->with('i')
            ->willReturnSelf();
        $this->total = $this->getMockBuilder('\Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->total
            ->expects($this->once())
            ->method('select')
            ->with('COUNT(DISTINCT i)')
            ->willReturnSelf();

        $repository = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Repository\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->expects($this->at(0))
            ->method('createQueryBuilder')
            ->with('i')
            ->willReturn($this->select);
        $repository
            ->expects($this->at(1))
            ->method('createQueryBuilder')
            ->with('i')
            ->willReturn($this->total);

        $doctrine = $this->getMockBuilder('\Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $doctrine
            ->expects($this->atLeastOnce())
            ->method('getRepository')
            ->with('AnimeDbCatalogBundle:Item')
            ->willReturn($repository);

        $this->builder = new Builder($doctrine);
    }

    /**
     * Test get query select
     */
    public function testGetQuerySelect()
    {
        $this->assertEquals($this->select, $this->builder->getQuerySelect());
    }

    /**
     * Test get query total
     */
    public function testGetQueryTotal()
    {
        $this->assertEquals($this->total, $this->builder->getQueryTotal());
    }
}
