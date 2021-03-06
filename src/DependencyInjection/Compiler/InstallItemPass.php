<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Install item compiler pass.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class InstallItemPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (
            $container->getParameter('anime_db.catalog.installed') ||
            !$container->has('anime_db.install.item.chain')
        ) {
            return;
        }

        $definition = $container->findDefinition('anime_db.install.item.chain');
        $taggedServices = $container->findTaggedServiceIds('anime_db.install_item');
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                !empty($attributes[0]['debug']) ? 'addDebugItem' : 'addPublicItem',
                [new Reference($id)]
            );
        }
    }
}
