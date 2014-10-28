<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Plugin\Fill\Filler;

use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Filler as FillerForm;

/**
 * Test filler plugin
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Plugin\Fill\Filler
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class FillerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Filler
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filler;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->filler = $this->getMockForAbstractClass('AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler');
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
            ->willReturn($child)
            ->with('foo', [
                'route' => 'fill_filler',
                'routeParameters' => ['plugin' => 'bar']
            ]);
        $this->filler
            ->expects($this->once())
            ->method('getTitle')
            ->willReturn('foo');
        $this->filler
            ->expects($this->once())
            ->method('getName')
            ->willReturn('bar');
        $this->assertEquals($child, $this->filler->buildMenu($item));
    }

    /**
     * Test get form
     */
    public function testGetForm()
    {
        $this->assertInstanceOf('\AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Filler', $this->filler->getForm());
    }

    /**
     * Test get link for fill fail
     *
     * @expectedException \LogicException
     */
    public function testGetLinkForFillFail()
    {
        $this->filler->getLinkForFill([]);
    }

    /**
     * Test get link for fill
     */
    public function testGetLinkForFill()
    {
        $router = $this->getMockBuilder('\Symfony\Bundle\FrameworkBundle\Routing\Router')
            ->disableOriginalConstructor()
            ->getMock();
        $router
            ->expects($this->once())
            ->method('generate')
            ->willReturn('my_url')
            ->with('fill_filler', [
                'plugin' => 'foo',
                FillerForm::FORM_NAME => ['url' => ['my_data']]
            ]);
        $this->filler
            ->expects($this->once())
            ->method('getName')
            ->willReturn('foo');
        $this->filler->setRouter($router);
        $this->assertEquals('my_url', $this->filler->getLinkForFill(['my_data']));
    }

    /**
     * Get links
     *
     * @return array
     */
    public function getLinks()
    {
        return [
            ['http://example.com', []],
            ['http://example.com/?foo=bar', []],
            ['http://example.com/?'.http_build_query([FillerForm::FORM_NAME => []]), []]
        ];
    }

    /**
     * Test fill from search result fail
     *
     * @dataProvider getLinks
     *
     * @param string $link
     * @param array $data
     */
    public function testFillFromSearchResultFail($link, array $data)
    {
        $item = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $item
            ->expects($this->once())
            ->method('getLink')
            ->willReturn($link);
        $this->assertNull($this->filler->fillFromSearchResult($item));
    }

    /**
     * Test fill from search result
     */
    public function testFillFromSearchResult()
    {
        $data = ['foo', 'bar'];
        $link = 'http://example.com/?'.http_build_query([FillerForm::FORM_NAME => $data]);
        $result = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item');
        $item = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $item
            ->expects($this->once())
            ->method('getLink')
            ->willReturn($link);
        $this->filler
            ->expects($this->once())
            ->method('fill')
            ->willReturn($result)
            ->with($data);
        $this->assertEquals($result, $this->filler->fillFromSearchResult($item));
    }
}
