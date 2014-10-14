<?php
/**
 * AnimeDb package
*
* @package   AnimeDb
* @author    Peter Gribanov <info@peter-gribanov.ru>
* @copyright Copyright (c) 2011, Peter Gribanov
* @license   http://opensource.org/licenses/GPL-3.0 GPL v3
*/

namespace AnimeDb\Bundle\CatalogBundle\Tests\DependencyInjection;

use AnimeDb\Bundle\CatalogBundle\DependencyInjection\AnimeDbCatalogExtension;

/**
 * Test DependencyInjection
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\DependencyInjection
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class AnimeDbCatalogExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test load
     */
    public function testLoad()
    {
        $di = new AnimeDbCatalogExtension();
        $di->load([], $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder'));
    }
}
