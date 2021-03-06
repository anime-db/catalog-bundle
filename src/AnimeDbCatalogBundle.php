<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use AnimeDb\Bundle\CatalogBundle\DependencyInjection\Compiler\PluginPass;
use AnimeDb\Bundle\CatalogBundle\DependencyInjection\Compiler\InstallItemPass;

/**
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class AnimeDbCatalogBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new PluginPass());
        $container->addCompilerPass(new InstallItemPass());
    }
}
