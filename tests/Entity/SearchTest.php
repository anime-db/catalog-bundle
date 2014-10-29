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

use AnimeDb\Bundle\CatalogBundle\Entity\Search;

/**
 * Test search
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class SearchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Search
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Search
     */
    protected $search;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->search = new Search();
    }

    /**
     * Get add methods
     *
     * @return array
     */
    public function getAddMethods()
    {
        return [
            [
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Genre'),
                'getGenres',
                'addGenre',
                'removeGenre'
            ],
            [
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Label'),
                'getLabels',
                'addLabel',
                'removeLabel'
            ],
        ];
    }

    /**
     * Test add entity
     *
     * @dataProvider getAddMethods
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $entity
     * @param string $get
     * @param string $add
     * @param string $remove
     */
    public function testAdd(\PHPUnit_Framework_MockObject_MockObject $entity, $get, $add, $remove)
    {
        $this->assertEmpty(call_user_func([$this->search, $get]));

        // add
        $this->assertEquals($this->search, call_user_func([$this->search, $add], $entity));
        $this->assertEquals($this->search, call_user_func([$this->search, $add], $entity));
        /* @var $coll \Doctrine\Common\Collections\ArrayCollection */
        $coll = call_user_func([$this->search, $get]);
        $this->assertEquals(1, $coll->count());
        $this->assertEquals($entity, $coll->first());

        // remove
        $this->assertEquals($this->search, call_user_func([$this->search, $remove], $entity));
        $this->assertEmpty(call_user_func([$this->search, $get]));
    }

    /**
     * Get set methods
     *
     * @return array
     */
    public function getSetMethods()
    {
        return [
            [
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Country'),
                'getCountry',
                'setCountry'
            ],
            [
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage'),
                'getStorage',
                'setStorage'
            ],
            [
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Type'),
                'getType',
                'setType'
            ],
            [
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Studio'),
                'getStudio',
                'setStudio'
            ],
        ];
    }

    /**
     * Test set entity
     *
     * @dataProvider getSetMethods
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $entity
     * @param string $get
     * @param string $set
     */
    public function testSet(\PHPUnit_Framework_MockObject_MockObject $entity, $get, $set)
    {
        $this->assertNull(call_user_func([$this->search, $get]));

        // set
        $this->assertEquals($this->search, call_user_func([$this->search, $set], $entity));
        $this->assertEquals($this->search, call_user_func([$this->search, $set], $entity));
        $this->assertEquals($entity, call_user_func([$this->search, $get]));

        // remove
        $this->assertEquals($this->search, call_user_func([$this->search, $set], null));
        $this->assertNull(call_user_func([$this->search, $get]));
    }
}
