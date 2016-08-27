<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Tests\Event\Listener;

use AnimeDb\Bundle\CatalogBundle\Event\Listener\Package;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Test package listener.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class PackageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getEvents()
    {
        return [
            [
                $this
                    ->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Event\Package\Updated')
                    ->disableOriginalConstructor()
                    ->getMock(),
                'onUpdate',
                'foo',
            ],
            [
                $this
                    ->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Event\Package\Installed')
                    ->disableOriginalConstructor()
                    ->getMock(),
                'onInstall',
                'foo',
            ],
            [
                $this
                    ->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Event\Package\Updated')
                    ->disableOriginalConstructor()
                    ->getMock(),
                'onUpdate',
                'anime-db/catalog-bundle',
            ],
            [
                $this
                    ->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Event\Package\Installed')
                    ->disableOriginalConstructor()
                    ->getMock(),
                'onInstall',
                'anime-db/catalog-bundle',
            ],
        ];
    }

    /**
     * @dataProvider getEvents
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $event
     * @param string $method
     * @param string $package_name
     */
    public function testCopyTemplates(\PHPUnit_Framework_MockObject_MockObject $event, $method, $package_name)
    {
        $package = $this->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $package
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($package_name));
        $event
            ->expects($this->once())
            ->method('getPackage')
            ->will($this->returnValue($package));

        /* @var $fs \PHPUnit_Framework_MockObject_MockObject|Filesystem */
        $fs = $this->getMock('\Symfony\Component\Filesystem\Filesystem');
        /* @var $kernel \PHPUnit_Framework_MockObject_MockObject|Kernel */
        $kernel = $this
            ->getMockBuilder('\Symfony\Component\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock();
        if ($package_name == 'anime-db/catalog-bundle') {
            $from = '/from/';
            $to = '/root/dir/Resources/';
            $kernel
                ->expects($this->once())
                ->method('locateResource')
                ->will($this->returnValue($from))
                ->with('@AnimeDbCatalogBundle/Resources/views/');
            $fs
                ->expects($this->at(0))
                ->method('copy')
                ->with($from.'errors/error.html.twig', $to.'TwigBundle/views/Exception/error.html.twig', true);
            $fs
                ->expects($this->at(1))
                ->method('copy')
                ->with($from.'errors/error404.html.twig', $to.'TwigBundle/views/Exception/error404.html.twig', true);
        } else {
            $fs
                ->expects($this->never())
                ->method('copy');
        }

        // test
        $listener = new Package($kernel, $fs, '/root/dir');
        call_user_func([$listener, $method], $event);
    }
}
