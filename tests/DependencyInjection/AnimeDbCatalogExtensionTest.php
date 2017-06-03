<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\DependencyInjection;

use AnimeDb\Bundle\CatalogBundle\DependencyInjection\AnimeDbCatalogExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Test DependencyInjection.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class AnimeDbCatalogExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        /* @var $container \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder */
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $di = new AnimeDbCatalogExtension();
        $di->load([], $container);
    }
}
