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
 * Plugin compiler pass.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class PluginPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->compilerChain($container, 'anime_db.plugin.filler', 'anime_db.filler');
        $this->compilerChain($container, 'anime_db.plugin.search_fill', 'anime_db.search');
        $this->compilerChain($container, 'anime_db.plugin.refiller', 'anime_db.refiller');
        $this->compilerChain($container, 'anime_db.plugin.import', 'anime_db.import');
        $this->compilerChain($container, 'anime_db.plugin.export', 'anime_db.export');
        $this->compilerChain($container, 'anime_db.plugin.item', 'anime_db.item');
        $this->compilerChain($container, 'anime_db.plugin.setting', 'anime_db.setting');
    }

    /**
     * @param ContainerBuilder $container
     * @param string $chain_name
     * @param string $tag
     */
    private function compilerChain(ContainerBuilder $container, $chain_name, $tag)
    {
        if (!$container->has($chain_name)) {
            return;
        }

        $definition = $container->findDefinition($chain_name);
        $taggedServices = $container->findTaggedServiceIds($tag);
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall('addPlugin', [new Reference($id)]);
        }
    }
}
