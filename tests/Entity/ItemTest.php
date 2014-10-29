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

use AnimeDb\Bundle\CatalogBundle\Entity\Item;

/**
 * Test item
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Item
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    protected $item;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->item = new Item();
    }

    /**
     * Test do change date update
     */
    public function testDoChangeDateUpdate()
    {
        $date = (new \DateTime())->modify('+100 seconds');
        $this->item->setDateUpdate($date);

        $this->item->doChangeDateUpdate();
        $this->assertInstanceOf('\DateTime', $this->item->getDateUpdate());
        $this->assertNotEquals($date, $this->item->getDateUpdate());
    }

    /**
     * Get required paths
     *
     * @return array
     */
    public function getRequiredPaths()
    {
        return [
            [false, false, ''],
            [true, false, ''],
            [true, true, ''],
            [true, true, 'foo']
        ];
    }

    /**
     * Test is path valid
     *
     * @dataProvider getRequiredPaths
     *
     * @param boolean $storage
     * @param boolean $required
     * @param string $path
     */
    public function testIsPathValid($storage, $required, $path)
    {
        if ($storage) {
            $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
            $storage
                ->expects($this->once())
                ->method('isPathRequired')
                ->willReturn($required);
            $this->item->setStorage($storage);
        }
        $context = $this->getMock('\Symfony\Component\Validator\ExecutionContextInterface');
        $context
            ->expects($storage && $required && !$path ? $this->once() : $this->never())
            ->method('addViolationAt')
            ->with('path', 'Path is required to fill for current type of storage');
        $this->item->setPath($path);
        $this->item->isPathValid($context);
    }

    /**
     * Get url names
     *
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
     * Test get url name
     *
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
