<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Listener;

use AnimeDb\Bundle\AnimeDbBundle\Event\Package\Updated;
use AnimeDb\Bundle\AnimeDbBundle\Event\Package\Installed;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Package listener
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Listener
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Package
{
    /**
     * Root dir
     *
     * @var string
     */
    protected $root_dir;

    /**
     * Kernel
     *
     * @var \Symfony\Component\HttpKernel\Kernel
     */
    protected $kernel;

    /**
     * Construct
     *
     * @param \Symfony\Component\HttpKernel\Kernel $kernel
     * @param string $root_dir
     */
    public function __construct(Kernel $kernel, $root_dir) {
        $this->kernel = $kernel;
        $this->root_dir = $root_dir;
    }

    /**
     * On update package
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\Package\Updated $event
     */
    public function onUpdate(Updated $event)
    {
        if ($event->getPackage()->getName() == 'anime-db/catalog-bundle') {
            $this->copyTemplates();
        }
    }

    /**
     * On install package
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\Package\Installed $event
     */
    public function onInstall(Installed $event)
    {
        if ($event->getPackage()->getName() == 'anime-db/catalog-bundle') {
            $this->copyTemplates();
        }
    }

    /**
     * Copy templates
     */
    protected function copyTemplates()
    {
        $from = $this->kernel->locateResource('@AnimeDbCatalogBundle/Resources/views/');
        $to = $this->root_dir.'/Resources/';
        // overwrite knp menu tpl
        copy($from.'knp_menu.html.twig', $to.'views/knp_menu.html.twig');
        // overwrite twig error tpls
        if (!file_exists($to.'TwigBundle/views/Exception/')) {
            mkdir($to.'TwigBundle/views/Exception/', 0755, true);
        }
        copy($from.'errors/error.html.twig', $to.'TwigBundle/views/Exception/error.html.twig');
        copy($from.'errors/error404.html.twig', $to.'TwigBundle/views/Exception/error404.html.twig');
    }
}
