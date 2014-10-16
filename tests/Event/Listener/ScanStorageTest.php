<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Event\Listener;

use AnimeDb\Bundle\CatalogBundle\Event\Listener\ScanStorage;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Search as SearchPluginForm;

/**
 * Test ScanStorage listener
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Event\Listener
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ScanStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Listener
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Event\Listener\ScanStorage
     */
    protected $listener;

    /**
     * Entity manager
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $em;

    /**
     * Templating
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $templating;

    /**
     * Search chain
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $search;

    /**
     * Router
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $router;

    /**
     * Form factory
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $form_factory;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->templating = $this->getMockBuilder('\Symfony\Bundle\TwigBundle\TwigEngine')
            ->disableOriginalConstructor()
            ->getMock();
        $this->search = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain')
            ->disableOriginalConstructor()
            ->getMock();
        $this->router = $this->getMockBuilder('\Symfony\Bundle\FrameworkBundle\Routing\Router')
            ->disableOriginalConstructor()
            ->getMock();
        $this->form_factory = $this->getMockBuilder('\Symfony\Component\Form\FormFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->listener = new ScanStorage(
            $this->em,
            $this->templating,
            $this->search,
            $this->router,
            $this->form_factory
        );
    }

    /**
     * Get notices
     *
     * @return array
     */
    public function getNotices()
    {
        $dif_item = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item');
        $dif = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Event\Storage\DeleteItemFiles')
            ->disableOriginalConstructor()
            ->getMock();
        $dif
            ->expects($this->once())
            ->method('getItem')
            ->willReturn($dif_item);

        $uif_item = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item');
        $uif = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Event\Storage\UpdateItemFiles')
            ->disableOriginalConstructor()
            ->getMock();
        $uif
            ->expects($this->once())
            ->method('getItem')
            ->willReturn($uif_item);

        $ani_item = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item');
        $ani_storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $ani_item
            ->expects($this->once())
            ->method('getStorage')
            ->willReturn($ani_storage);
        $ani = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Event\Storage\AddNewItem')
            ->disableOriginalConstructor()
            ->getMock();
        $ani
            ->expects($this->atLeastOnce())
            ->method('getItem')
            ->willReturn($ani_item);

        return [
            [
                $dif,
                ['item' => $dif_item],
                'onDeleteItemFiles',
                ScanStorage::NOTICE_TYPE_ITEM_FILES_NOT_FOUND
            ],
            [
                $uif,
                ['item' => $uif_item],
                'onUpdateItemFiles',
                ScanStorage::NOTICE_TYPE_UPDATED_ITEM_FILES
            ],
            [
                $ani,
                ['storage' => $ani_storage, 'item' => $ani_item],
                'onAddNewItemSendNotice',
                ScanStorage::NOTICE_TYPE_ADDED_NEW_ITEM
            ]
        ];
    }

    /**
     * Test send notices
     *
     * @dataProvider getNotices
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $event
     * @param array $params
     * @param string $method
     * @param string $type
     */
    public function testSendNotices(
        \PHPUnit_Framework_MockObject_MockObject $event,
        array $params,
        $method,
        $type
    ) {
        $that = $this;
        $this->templating
            ->expects($this->once())
            ->method('render')
            ->willReturnCallback(function ($received_tpl, $received_params) use ($that, $type, $params) {
                $that->assertEquals('AnimeDbCatalogBundle:Notice:messages/'.$type.'.html.twig', $received_tpl);
                $that->assertEquals($params, $received_params);
                return 'foo';
            });
        $this->em
            ->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function ($notice) use ($that, $type) {
                /* @var $notice \AnimeDb\Bundle\AppBundle\Entity\Notice */
                $that->assertInstanceOf('\AnimeDb\Bundle\AppBundle\Entity\Notice', $notice);
                $that->assertEquals($type, $notice->getType());
                $that->assertEquals('foo', $notice->getMessage());
            });

        call_user_func([$this->listener, $method], $event);
    }

    /**
     * Test on detected new files send notice propagation stopped
     */
    public function testOnDetectedNewFilesSendNoticePropagationStopped()
    {
        $event = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Event\Storage\DetectedNewFiles')
            ->disableOriginalConstructor()
            ->getMock();
        $event
            ->expects($this->once())
            ->method('isPropagationStopped')
            ->willReturn(true);
        $this->em
            ->expects($this->never())
            ->method('persist');

        $this->listener->onDetectedNewFilesSendNotice($event);
    }

    /**
     * Get search plugins
     *
     * @return array
     */
    public function getSearchPlugins()
    {
        return [
            [$this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search'), false],
            [$this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search'), true],
            [null, false],
            [null, true]
        ];
    }

    /**
     * Test on detected new files send notice
     *
     * @dataProvider getSearchPlugins
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $dafeult_plugin
     * @param boolean $has_plugins
     */
    public function testOnDetectedNewFilesSendNotice(
        \PHPUnit_Framework_MockObject_MockObject $dafeult_plugin = null,
        $has_plugins
    ) {
        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $event = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Event\Storage\DetectedNewFiles')
            ->disableOriginalConstructor()
            ->getMock();
        $event
            ->expects($this->once())
            ->method('isPropagationStopped')
            ->willReturn(false);
        $event
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('bar');
        $event
            ->expects($this->once())
            ->method('getStorage')
            ->willReturn($storage);
        $this->search
            ->expects($this->once())
            ->method('getDafeultPlugin')
            ->willReturn($dafeult_plugin);
        $link = null;
        if ($dafeult_plugin) {
            $dafeult_plugin
                ->expects($this->once())
                ->method('getLinkForSearch')
                ->willReturn($link = 'foo')
                ->with('bar');
        } else {
            $this->search
                ->expects($this->once())
                ->method('hasPlugins')
                ->willReturn($has_plugins);
            if ($has_plugins) {
                $this->router
                    ->expects($this->once())
                    ->method('generate')
                    ->willReturn($link = 'baz')
                    ->with(
                        'fill_search_in_all',
                        [SearchPluginForm::FORM_NAME => ['name' => 'bar']]
                    );
            }
        }

        $this->testSendNotices(
            $event,
            ['storage' => $storage, 'name' => 'bar', 'link' => $link],
            'onDetectedNewFilesSendNotice',
            ScanStorage::NOTICE_TYPE_DETECTED_FILES_FOR_NEW_ITEM
        );
    }

    /**
     * Test on add new item persist it
     */
    public function testOnAddNewItemPersistIt()
    {
        $item = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item');
        $event = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Event\Storage\AddNewItem')
            ->disableOriginalConstructor()
            ->getMock();
        $event
            ->expects($this->once())
            ->method('getItem')
            ->willReturn($item);
        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with($item);
        $this->em
            ->expects($this->once())
            ->method('flush');

        $this->listener->onAddNewItemPersistIt($event);
    }
}
