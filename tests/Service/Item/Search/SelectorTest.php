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

use AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector;

/**
 * Test selector
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Service\Item\Search
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class SelectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test create
     */
    public function testCreate()
    {
        $query_builder = $this->getMockBuilder('\Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $repository = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Repository\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->expects($this->atLeastOnce())
            ->method('createQueryBuilder')
            ->with('i')
            ->willReturn($query_builder);
        $doctrine = $this->getMockBuilder('\Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $doctrine
            ->expects($this->atLeastOnce())
            ->method('getRepository')
            ->with('AnimeDbCatalogBundle:Item')
            ->willReturn($repository);

        // test
        $builder = (new Selector($doctrine))->create();
        $this->assertInstanceOf('\AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector\Builder', $builder);
    }
}
