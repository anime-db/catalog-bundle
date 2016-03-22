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
use AnimeDb\Bundle\CatalogBundle\Event\Storage\StoreEvents;

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
        touch(sys_get_temp_dir().'/test');
    }

    protected function tearDown()
    {
        unlink(sys_get_temp_dir().'/test');
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
            ->will($this->returnValue($dif_item));

        $uif_item = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item');
        $uif = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Event\Storage\UpdateItemFiles')
            ->disableOriginalConstructor()
            ->getMock();
        $uif
            ->expects($this->once())
            ->method('getItem')
            ->will($this->returnValue($uif_item));

        $ani_item = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item');
        $ani_storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $ani_item
            ->expects($this->once())
            ->method('getStorage')
            ->will($this->returnValue($ani_storage));
        $ani = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Event\Storage\AddNewItem')
            ->disableOriginalConstructor()
            ->getMock();
        $ani
            ->expects($this->atLeastOnce())
            ->method('getItem')
            ->will($this->returnValue($ani_item));

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
                ScanStorage::NOTICE_TYPE_UPDATE_ITEM_FILES
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
            ->will($this->returnCallback(function ($received_tpl, $received_params) use ($that, $type, $params) {
                $that->assertEquals('AnimeDbCatalogBundle:Notice:messages/'.$type.'.html.twig', $received_tpl);
                $that->assertEquals($params, $received_params);
                return 'foo';
            }));
        $this->em
            ->expects($this->once())
            ->method('persist')
            ->will($this->returnCallback(function ($notice) use ($that, $type) {
                /* @var $notice \AnimeDb\Bundle\AppBundle\Entity\Notice */
                $that->assertInstanceOf('\AnimeDb\Bundle\AppBundle\Entity\Notice', $notice);
                $that->assertEquals($type, $notice->getType());
                $that->assertEquals('foo', $notice->getMessage());
            }));

        call_user_func([$this->listener, $method], $event);
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
     * @param bool $has_plugins
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
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue('bar'));
        $event
            ->expects($this->once())
            ->method('getStorage')
            ->will($this->returnValue($storage));
        $this->search
            ->expects($this->once())
            ->method('getDafeultPlugin')
            ->will($this->returnValue($dafeult_plugin));
        $link = null;
        if ($dafeult_plugin) {
            $dafeult_plugin
                ->expects($this->once())
                ->method('getLinkForSearch')
                ->will($this->returnValue($link = 'foo'))
                ->with('bar');
        } else {
            $this->search
                ->expects($this->once())
                ->method('hasPlugins')
                ->will($this->returnValue($has_plugins));
            if ($has_plugins) {
                $this->router
                    ->expects($this->once())
                    ->method('generate')
                    ->will($this->returnValue($link = 'baz'))
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
     * Get plugins
     *
     * @return array
     */
    public function getPlugins()
    {
        return [
            [
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search'),
                true,
                true,
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search'),
                false,
                false
            ],
            [
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search'),
                true,
                false,
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search'),
                false,
                false
            ],
            [
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search'),
                false,
                false,
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search'),
                true,
                true
            ],
            [
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search'),
                false,
                false,
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search'),
                true,
                false
            ],
            [
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search'),
                false,
                false,
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search'),
                false,
                false
            ]
        ];
    }

    /**
     * Test on detected new files try add
     *
     * @dataProvider getPlugins
     *
     * @param bool $is_dir
     */
    public function testOnDetectedNewFilesTryAdd(
        $default,
        $default_is_added,
        $default_is_dir,
        $second,
        $second_is_added,
        $second_is_dir
    ) {
        $event = $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Event\Storage\DetectedNewFiles')
            ->disableOriginalConstructor()
            ->getMock();
        $this->search
            ->expects($this->once())
            ->method('getDafeultPlugin')
            ->will($this->returnValue($default));
        $this->tryAddItem($default, $event, $default_is_added, $default_is_dir);

        if ($default_is_added) {
            $this->search
                ->expects($this->never())
                ->method('getPlugins');
        } else {
            $this->search
                ->expects($this->once())
                ->method('getPlugins')
                ->will($this->returnValue([$default, $second]));
            $this->tryAddItem($second, $event, $second_is_added, $second_is_dir);
        }

        if (!$default_is_added && !$second_is_added) {
            $event
                ->expects($this->never())
                ->method('stopPropagation');
        }

        $this->listener->onDetectedNewFilesTryAdd($event);
    }

    /**
     * Try add item
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $plugin
     * @param \PHPUnit_Framework_MockObject_MockObject $event
     * @param bool $is_added
     * @param bool $is_dir
     */
    protected function tryAddItem(
        \PHPUnit_Framework_MockObject_MockObject $plugin,
        \PHPUnit_Framework_MockObject_MockObject $event,
        $is_added,
        $is_dir
    ) {
        $event
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $item = null;
        if ($is_added) {
            $that = $this;
            $filler = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler');
            $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
            $item = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item');
            $item
                ->expects($this->atLeastOnce())
                ->method('setStorage')
                ->with($storage);
            $item
                ->expects($this->atLeastOnce())
                ->method('setPath')
                ->with('/tmp/bar'.($is_dir ? DIRECTORY_SEPARATOR : ''));
            $dispatcher = $this->getMock('\Symfony\Component\EventDispatcher\EventDispatcherInterface');
            $dispatcher
                ->expects($this->atLeastOnce())
                ->method('dispatch')
                ->will($this->returnCallback(function ($event_name, $event) use ($that, $item, $filler) {
                    $that->assertEquals(StoreEvents::ADD_NEW_ITEM, $event_name);
                    /* @var $event \AnimeDb\Bundle\CatalogBundle\Event\Storage\AddNewItem */
                    $that->assertInstanceOf('\AnimeDb\Bundle\CatalogBundle\Event\Storage\AddNewItem', $event);
                    $that->assertEquals($item, $event->getItem());
                    $that->assertEquals([$filler], $event->getFillers()->toArray());
                }));
            $file = $this->getMockBuilder('\Symfony\Component\Finder\SplFileInfo')
                ->setConstructorArgs([sys_get_temp_dir().'/test', '', ''])
                ->getMock();
            $file
                ->expects($this->atLeastOnce())
                ->method('getPathname')
                ->will($this->returnValue('/tmp/bar'));
            $file
                ->expects($this->atLeastOnce())
                ->method('isDir')
                ->will($this->returnValue($is_dir));
            $event
                ->expects($this->atLeastOnce())
                ->method('getStorage')
                ->will($this->returnValue($storage));
            $event
                ->expects($this->atLeastOnce())
                ->method('getFile')
                ->will($this->returnValue($file));
            $event
                ->expects($this->atLeastOnce())
                ->method('stopPropagation');
            $event
                ->expects($this->atLeastOnce())
                ->method('getDispatcher')
                ->will($this->returnValue($dispatcher));
            $plugin
                ->expects($this->atLeastOnce())
                ->method('getFiller')
                ->will($this->returnValue($filler));
        }
        $plugin
            ->expects($this->atLeastOnce())
            ->method('getCatalogItem')
            ->will($this->returnValue($item))
            ->with('foo');
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
            ->will($this->returnValue($item));
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
