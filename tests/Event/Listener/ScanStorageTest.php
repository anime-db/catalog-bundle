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
                ScanStorage::NOTICE_TYPE_ITEM_FILES_NOT_FOUND,
                'AnimeDbCatalogBundle:Notice:messages/delete_item_files.html.twig'
            ],
            [
                $uif,
                ['item' => $uif_item],
                'onUpdateItemFiles',
                ScanStorage::NOTICE_TYPE_UPDATED_ITEM_FILES,
                'AnimeDbCatalogBundle:Notice:messages/update_item_files.html.twig'
            ],
            [
                $ani,
                ['storage' => $ani_storage, 'item' => $ani_item],
                'onAddNewItemSendNotice',
                ScanStorage::NOTICE_TYPE_ADDED_NEW_ITEM,
                'AnimeDbCatalogBundle:Notice:messages/added_new_item.html.twig'
            ]
        ];
    }

    /**
     * Test persist notice
     *
     * @dataProvider getNotices
     */
    public function testPersistNotice(
        \PHPUnit_Framework_MockObject_MockObject $event,
        array $params,
        $method,
        $type,
        $tpl
    ) {
        $that = $this;
        $this->templating
            ->expects($this->once())
            ->method('render')
            ->willReturnCallback(function ($received_tpl, $received_params) use ($that, $tpl, $params) {
                $that->assertEquals($tpl, $received_tpl);
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
}
