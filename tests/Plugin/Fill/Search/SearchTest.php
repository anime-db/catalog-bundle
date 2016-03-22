<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Plugin\Fill\Search;

use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Search as SearchForm;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Filler as FillerForm;

/**
 * Test search plugin
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Plugin\Fill\Search
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class SearchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Search
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $search;

    protected function setUp()
    {
        $this->search = $this->getMockForAbstractClass('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search');
    }

    /**
     * Test build menu
     */
    public function testBuildMenu()
    {
        $child = $this->getMock('\Knp\Menu\ItemInterface');
        $item = $this->getMock('\Knp\Menu\ItemInterface');
        $item
            ->expects($this->once())
            ->method('addChild')
            ->will($this->returnValue($child))
            ->with('foo', [
                'route' => 'fill_search',
                'routeParameters' => ['plugin' => 'bar']
            ]);
        $this->search
            ->expects($this->once())
            ->method('getTitle')
            ->will($this->returnValue('foo'));
        $this->search
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('bar'));
        $this->assertEquals($child, $this->search->buildMenu($item));
    }

    /**
     * Test get form
     */
    public function testGetForm()
    {
        $this->assertInstanceOf('\AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Search', $this->search->getForm());
    }

    /**
     * Test set/get Filler
     */
    public function testFiller()
    {
        $filler = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler')
            ->disableOriginalConstructor()
            ->getMock();
        $this->assertNull($this->search->getFiller());
        $this->search->setFiller($filler);
        $this->assertEquals($filler, $this->search->getFiller());
    }

    /**
     * Test get link for fill from filler
     */
    public function testGetLinkForFillFromFiller()
    {
        $filler = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler')
            ->disableOriginalConstructor()
            ->getMock();
        $filler
            ->expects($this->once())
            ->method('getLinkForFill')
            ->will($this->returnValue('my_url'))
            ->with(['my_data']);
        $this->search->setFiller($filler);
        $this->assertEquals('my_url', $this->search->getLinkForFill(['my_data']));
    }

    /**
     * Test get link for fill from router
     */
    public function testGetLinkForFillFromRouter()
    {
        $router = $this->getMockBuilder('\Symfony\Bundle\FrameworkBundle\Routing\Router')
            ->disableOriginalConstructor()
            ->getMock();
        $router
            ->expects($this->once())
            ->method('generate')
            ->will($this->returnValue('my_url'))
            ->with(
                'fill_filler',
                [
                    'plugin' => 'foo',
                    FillerForm::FORM_NAME => ['url' => ['my_data']]
                ]
            );
        $this->search->setRouter($router);
        $this->search
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $this->assertEquals('my_url', $this->search->getLinkForFill(['my_data']));
    }

    /**
     * Test get link for search
     */
    public function testGetLinkForSearch()
    {
        $router = $this->getMockBuilder('\Symfony\Bundle\FrameworkBundle\Routing\Router')
            ->disableOriginalConstructor()
            ->getMock();
        $router
            ->expects($this->once())
            ->method('generate')
            ->will($this->returnValue('my_url'))
            ->with(
                'fill_search',
                [
                    'plugin' => 'foo',
                    SearchForm::FORM_NAME => ['name' => 'bar']
                ]
            );
        $this->search->setRouter($router);
        $this->search
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $this->assertEquals('my_url', $this->search->getLinkForSearch('bar'));
    }

    /**
     * Test get catalog item no filler
     */
    public function testGetCatalogItemNoFiller()
    {
        $this->assertNull($this->search->getCatalogItem('foo'));
    }

    /**
     * Test get catalog item fail search
     */
    public function testGetCatalogItemFailSearch()
    {
        $filler = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler')
            ->disableOriginalConstructor()
            ->getMock();
        $this->search->setFiller($filler);
        $this->search
            ->expects($this->once())
            ->method('search')
            ->willThrowException(new \Exception())
            ->with(['name' => 'foo']);
        $this->assertNull($this->search->getCatalogItem('foo'));
    }

    /**
     * Test get catalog item fail fill
     */
    public function testGetCatalogItemFailFill()
    {
        $item = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $filler = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler')
            ->disableOriginalConstructor()
            ->getMock();
        $filler
            ->expects($this->once())
            ->method('fillFromSearchResult')
            ->willThrowException(new \Exception())
            ->with($item);
        $this->search->setFiller($filler);
        $this->search
            ->expects($this->once())
            ->method('search')
            ->will($this->returnValue([$item]))
            ->with(['name' => 'foo']);
        $this->assertNull($this->search->getCatalogItem('foo'));
    }

    /**
     * Get search results
     *
     * @return array
     */
    public function getSearchResults()
    {
        return [
            [[]],
            [[
                $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Item')
                    ->disableOriginalConstructor()
                    ->getMock(),
                $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Item')
                    ->disableOriginalConstructor()
                    ->getMock()
            ]]
        ];
    }

    /**
     * Test get catalog item cant fill
     *
     * @dataProvider getSearchResults
     *
     * @param array $results
     */
    public function testGetCatalogItemCantFill(array $results)
    {
        $filler = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler')
            ->disableOriginalConstructor()
            ->getMock();
        $filler
            ->expects($this->never())
            ->method('fillFromSearchResult');
        $this->search->setFiller($filler);
        $this->search
            ->expects($this->once())
            ->method('search')
            ->will($this->returnValue($results))
            ->with(['name' => 'foo']);
        $this->assertNull($this->search->getCatalogItem('foo'));
    }

    /**
     * Test get catalog item
     */
    public function testGetCatalogItem()
    {
        $item = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item');
        $search_item = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $filler = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler')
            ->disableOriginalConstructor()
            ->getMock();
        $filler
            ->expects($this->once())
            ->method('fillFromSearchResult')
            ->will($this->returnValue($item))
            ->with($search_item);
        $this->search->setFiller($filler);
        $this->search
            ->expects($this->once())
            ->method('search')
            ->will($this->returnValue([$search_item]))
            ->with(['name' => 'foo']);
        $this->assertEquals($item, $this->search->getCatalogItem('foo'));
    }
}
