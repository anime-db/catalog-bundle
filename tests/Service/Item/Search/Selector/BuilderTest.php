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
use Doctrine\Common\Collections\ArrayCollection;

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
     * Entity
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $entity;

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
        $this->entity = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Search');

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

    /**
     * Get names
     *
     * @return array
     */
    public function getNames()
    {
        return [
            ['', ''],
            ['foo', 'foo%'],
            ['BAR', 'bar%'],
            ['ПрИвЕт', 'привет%'],
            ['foo%', 'foo%%%'],
            ['foo%%', 'foo%%%']
        ];
    }

    /**
     * Test add name
     *
     * @dataProvider getNames
     *
     * @param string $name
     * @param string $expected
     */
    public function testAddName($name, $expected)
    {
        $this->entity
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($name);
        if ($name) {
            $that = $this;
            $this->add(function (\PHPUnit_Framework_MockObject_MockObject $query) use ($that, $expected) {
                $query
                    ->expects($that->once())
                    ->method('innerJoin')
                    ->willReturnSelf()
                    ->with('i.names', 'n');
                $query
                    ->expects($that->once())
                    ->method('andWhere')
                    ->willReturnSelf()
                    ->with('LOWER(i.name) LIKE :name OR LOWER(n.name) LIKE :name'); 
                $query
                    ->expects($that->once())
                    ->method('setParameter')
                    ->willReturnSelf()
                    ->with('name', $expected);
            });
        }

        $this->builder->addName($this->entity);
    }

    /**
     * Get dates
     *
     * @return array
     */
    public function getDates()
    {
        return [
            [null],
            [new \DateTime()]
        ];
    }

    /**
     * Test add date add
     *
     * @dataProvider getDates
     *
     * @param \DateTime|null $date
     */
    public function testAddDateAdd(\DateTime $date = null)
    {
        $this->entity
            ->expects($this->atLeastOnce())
            ->method('getDateAdd')
            ->willReturn($date);
        if ($date) {
            $that = $this;
            $this->add(function (\PHPUnit_Framework_MockObject_MockObject $query) use ($that, $date) {
                $query
                    ->expects($that->once())
                    ->method('andWhere')
                    ->willReturnSelf()
                    ->with('i.date_add >= :date_add');
                $query
                    ->expects($that->once())
                    ->method('setParameter')
                    ->willReturnSelf()
                    ->with('date_add', $date->format('Y-m-d'));
            });
        }

        $this->builder->addDateAdd($this->entity);
    }

    /**
     * Test add date premiere
     *
     * @dataProvider getDates
     *
     * @param \DateTime|null $date
     */
    public function testAddDatePremiere(\DateTime $date = null)
    {
        $this->entity
            ->expects($this->atLeastOnce())
            ->method('getDatePremiere')
            ->willReturn($date);
        if ($date) {
            $that = $this;
            $this->add(function (\PHPUnit_Framework_MockObject_MockObject $query) use ($that, $date) {
                $query
                    ->expects($that->once())
                    ->method('andWhere')
                    ->willReturnSelf()
                    ->with('i.date_premiere >= :date_premiere');
                $query
                    ->expects($that->once())
                    ->method('setParameter')
                    ->willReturnSelf()
                    ->with('date_premiere', $date->format('Y-m-d'));
            });
        }

        $this->builder->addDatePremiere($this->entity);
    }

    /**
     * Test add date end
     *
     * @dataProvider getDates
     *
     * @param \DateTime|null $date
     */
    public function testAddDateEnd(\DateTime $date = null)
    {
        $this->entity
            ->expects($this->atLeastOnce())
            ->method('getDateEnd')
            ->willReturn($date);
        if ($date) {
            $that = $this;
            $this->add(function (\PHPUnit_Framework_MockObject_MockObject $query) use ($that, $date) {
                $query
                    ->expects($that->once())
                    ->method('andWhere')
                    ->willReturnSelf()
                    ->with('i.date_end <= :date_end');
                $query
                    ->expects($that->once())
                    ->method('setParameter')
                    ->willReturnSelf()
                    ->with('date_end', $date->format('Y-m-d'));
            });
        }

        $this->builder->addDateEnd($this->entity);
    }

    /**
     * Get ids
     *
     * @return array
     */
    public function getIds()
    {
        return [
            [0],
            [33]
        ];
    }

    /**
     * Test add country
     *
     * @dataProvider getIds
     *
     * @param integer $id
     */
    public function testAddCountry($id)
    {
        if ($id) {
            $entity = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Country');
            $entity
                ->expects($this->atLeastOnce())
                ->method('getId')
                ->willReturn($id);
            $that = $this;
            $this->add(function (\PHPUnit_Framework_MockObject_MockObject $query) use ($that, $id) {
                $query
                    ->expects($that->once())
                    ->method('andWhere')
                    ->willReturnSelf()
                    ->with('i.country = :country');
                $query
                    ->expects($that->once())
                    ->method('setParameter')
                    ->willReturnSelf()
                    ->with('country', $id);
            });
            $this->entity
                ->expects($this->atLeastOnce())
                ->method('getCountry')
                ->willReturn($entity);
        } else {
            $this->entity
                ->expects($this->atLeastOnce())
                ->method('getCountry')
                ->willReturn(null);
        }

        $this->builder->addCountry($this->entity);
    }

    /**
     * Test add storage
     *
     * @dataProvider getIds
     *
     * @param integer $id
     */
    public function testAddStorage($id)
    {
        if ($id) {
            $entity = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
            $entity
                ->expects($this->atLeastOnce())
                ->method('getId')
                ->willReturn($id);
            $that = $this;
            $this->add(function (\PHPUnit_Framework_MockObject_MockObject $query) use ($that, $id) {
                $query
                    ->expects($that->once())
                    ->method('andWhere')
                    ->willReturnSelf()
                    ->with('i.storage = :storage');
                $query
                    ->expects($that->once())
                    ->method('setParameter')
                    ->willReturnSelf()
                    ->with('storage', $id);
            });
            $this->entity
                ->expects($this->atLeastOnce())
                ->method('getStorage')
                ->willReturn($entity);
        } else {
            $this->entity
                ->expects($this->atLeastOnce())
                ->method('getStorage')
                ->willReturn(null);
        }

        $this->builder->addStorage($this->entity);
    }

    /**
     * Test add type
     *
     * @dataProvider getIds
     *
     * @param integer $id
     */
    public function testAddType($id)
    {
        if ($id) {
            $entity = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Type');
            $entity
                ->expects($this->atLeastOnce())
                ->method('getId')
                ->willReturn($id);
            $that = $this;
            $this->add(function (\PHPUnit_Framework_MockObject_MockObject $query) use ($that, $id) {
                $query
                    ->expects($that->once())
                    ->method('andWhere')
                    ->willReturnSelf()
                    ->with('i.type = :type');
                $query
                    ->expects($that->once())
                    ->method('setParameter')
                    ->willReturnSelf()
                    ->with('type', $id);
            });
            $this->entity
                ->expects($this->atLeastOnce())
                ->method('getType')
                ->willReturn($entity);
        } else {
            $this->entity
                ->expects($this->atLeastOnce())
                ->method('getType')
                ->willReturn(null);
        }

        $this->builder->addType($this->entity);
    }

    /**
     * Test add studio
     *
     * @dataProvider getIds
     *
     * @param integer $id
     */
    public function testAddStudio($id)
    {
        if ($id) {
            $entity = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Studio');
            $entity
                ->expects($this->atLeastOnce())
                ->method('getId')
                ->willReturn($id);
            $that = $this;
            $this->add(function (\PHPUnit_Framework_MockObject_MockObject $query) use ($that, $id) {
                $query
                    ->expects($that->once())
                    ->method('andWhere')
                    ->willReturnSelf()
                    ->with('i.studio = :studio');
                $query
                    ->expects($that->once())
                    ->method('setParameter')
                    ->willReturnSelf()
                    ->with('studio', $id);
            });
            $this->entity
                ->expects($this->atLeastOnce())
                ->method('getStudio')
                ->willReturn($entity);
        } else {
            $this->entity
                ->expects($this->atLeastOnce())
                ->method('getStudio')
                ->willReturn(null);
        }

        $this->builder->addStudio($this->entity);
    }

    /**
     * Get ids lists
     *
     * @return array
     */
    public function getIdsLists()
    {
        return [
            [[]],
            [[1, 2, 3]]
        ];
    }

    /**
     * Test add genres
     *
     * @dataProvider getIdsLists
     *
     * @param array $ids
     */
    public function testAddGenres(array $ids)
    {
        $genres = new ArrayCollection();
        $this->entity
            ->expects($this->atLeastOnce())
            ->method('getGenres')
            ->willReturn($genres);
        if ($ids) {
            foreach ($ids as $id) {
                $genre = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Genre');
                $genre
                    ->expects($this->atLeastOnce())
                    ->method('getId')
                    ->willReturn($id);
                $genres->add($genre);
            }
            $that = $this;
            $this->add(function (\PHPUnit_Framework_MockObject_MockObject $query) use ($that, $ids) {
                $query
                    ->expects($that->once())
                    ->method('innerJoin')
                    ->willReturnSelf()
                    ->with('i.genres', 'g');
                $query
                    ->expects($that->once())
                    ->method('andWhere')
                    ->willReturnSelf()
                    ->with('g.id IN ('.implode(',', $ids).')');
            });
        }
        $this->builder->addGenres($this->entity);
    }

    /**
     * Test add labels
     *
     * @dataProvider getIdsLists
     *
     * @param array $ids
     */
    public function testAddLabels(array $ids)
    {
        $labels = new ArrayCollection();
        $this->entity
            ->expects($this->atLeastOnce())
            ->method('getLabels')
            ->willReturn($labels);
        if ($ids) {
            foreach ($ids as $id) {
                $label = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Label');
                $label
                    ->expects($this->atLeastOnce())
                    ->method('getId')
                    ->willReturn($id);
                $labels->add($label);
            }
            $that = $this;
            $this->add(function (\PHPUnit_Framework_MockObject_MockObject $query) use ($that, $ids) {
                $query
                    ->expects($that->once())
                    ->method('innerJoin')
                    ->willReturnSelf()
                    ->with('i.labels', 'l');
                $query
                    ->expects($that->once())
                    ->method('andWhere')
                    ->willReturnSelf()
                    ->with('l.id IN ('.implode(',', $ids).')');
            });
        }
        $this->builder->addLabels($this->entity);
    }

    /**
     * Test add data to queries
     *
     * @param \Closure $adder
     */
    protected function add(\Closure $adder)
    {
        $adder($this->select);
        $adder($this->total);
    }

    /**
     * Get numbers
     *
     * @return array
     */
    public function getNumbers()
    {
        return [
            [-1],
            [0],
            [1],
        ];
    }

    /**
     * Test limit
     *
     * @dataProvider getNumbers
     *
     * @param integer $number
     */
    public function testLimit($number)
    {
        $this->select
            ->expects($number > 0 ? $this->once() : $this->never())
            ->method('setMaxResults')
            ->with($number)
            ->willReturnSelf();

        $this->builder->limit($number);
    }

    /**
     * Test offset
     *
     * @dataProvider getNumbers
     *
     * @param integer $number
     */
    public function testOffset($number)
    {
        $this->select
            ->expects($number > 0 ? $this->once() : $this->never())
            ->method('setFirstResult')
            ->with($number)
            ->willReturnSelf();

        $this->builder->offset($number);
    }

    /**
     * Test sort
     */
    public function testSort()
    {
        $this->select
            ->expects($this->once())
            ->method('orderBy')
            ->with('i.my_column', 'my_direction')
            ->willReturnSelf();

        $this->builder->sort('my_column', 'my_direction');
    }
}
