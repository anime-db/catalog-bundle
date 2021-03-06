<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Plugin\Fill\Filler;

use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Filler as FillerForm;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Item as ItemSearch;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Knp\Menu\ItemInterface;

/**
 * Test filler plugin.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class FillerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Filler
     */
    protected $filler;

    protected function setUp()
    {
        $this->filler = $this->getMockForAbstractClass('AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler');
    }

    public function testBuildMenu()
    {
        $child = $this->getMock('\Knp\Menu\ItemInterface');
        /* @var $item \PHPUnit_Framework_MockObject_MockObject|ItemInterface */
        $item = $this->getMock('\Knp\Menu\ItemInterface');
        $item
            ->expects($this->once())
            ->method('addChild')
            ->will($this->returnValue($child))
            ->with('foo', [
                'route' => 'fill_filler',
                'routeParameters' => ['plugin' => 'bar'],
            ]);
        $this->filler
            ->expects($this->once())
            ->method('getTitle')
            ->will($this->returnValue('foo'));
        $this->filler
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('bar'));
        $this->assertEquals($child, $this->filler->buildMenu($item));
    }

    public function testGetForm()
    {
        $this->assertInstanceOf('\AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Filler', $this->filler->getForm());
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetLinkForFillFail()
    {
        $this->filler->getLinkForFill([]);
    }

    public function testGetLinkForFill()
    {
        /* @var $router \PHPUnit_Framework_MockObject_MockObject|Router */
        $router = $this->getMockBuilder('\Symfony\Bundle\FrameworkBundle\Routing\Router')
            ->disableOriginalConstructor()
            ->getMock();
        $router
            ->expects($this->once())
            ->method('generate')
            ->will($this->returnValue('my_url'))
            ->with('fill_filler', [
                'plugin' => 'foo',
                FillerForm::FORM_NAME => ['url' => ['my_data']],
            ]);
        $this->filler
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $this->filler->setRouter($router);
        $this->assertEquals('my_url', $this->filler->getLinkForFill(['my_data']));
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return [
            ['http://example.com', []],
            ['http://example.com/?foo=bar', []],
            ['http://example.com/?'.http_build_query([FillerForm::FORM_NAME => []]), []],
        ];
    }

    /**
     * @dataProvider getLinks
     *
     * @param string $link
     */
    public function testFillFromSearchResultFail($link)
    {
        /* @var $item \PHPUnit_Framework_MockObject_MockObject|ItemSearch */
        $item = $this
            ->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $item
            ->expects($this->once())
            ->method('getLink')
            ->will($this->returnValue($link));
        $this->assertNull($this->filler->fillFromSearchResult($item));
    }

    public function testFillFromSearchResult()
    {
        $data = ['foo', 'bar'];
        $link = 'http://example.com/?'.http_build_query([FillerForm::FORM_NAME => $data]);
        $result = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item');
        /* @var $item \PHPUnit_Framework_MockObject_MockObject|ItemSearch */
        $item = $this
            ->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $item
            ->expects($this->once())
            ->method('getLink')
            ->will($this->returnValue($link));
        $this->filler
            ->expects($this->once())
            ->method('fill')
            ->will($this->returnValue($result))
            ->with($data);
        $this->assertEquals($result, $this->filler->fillFromSearchResult($item));
    }
}
