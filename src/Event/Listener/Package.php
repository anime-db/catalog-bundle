<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Event\Listener;

use AnimeDb\Bundle\AnimeDbBundle\Event\Package\Updated;
use AnimeDb\Bundle\AnimeDbBundle\Event\Package\Installed;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Package listener.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Package
{
    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $root_dir;

    /**
     * @param Kernel $kernel
     * @param Filesystem $fs
     * @param string $root_dir
     */
    public function __construct(Kernel $kernel, Filesystem $fs, $root_dir)
    {
        $this->kernel = $kernel;
        $this->root_dir = $root_dir;
        $this->fs = $fs;
    }

    /**
     * @param Updated $event
     */
    public function onUpdate(Updated $event)
    {
        if ($event->getPackage()->getName() == 'anime-db/catalog-bundle') {
            $this->copyTemplates();
        }
    }

    /**
     * @param Installed $event
     */
    public function onInstall(Installed $event)
    {
        if ($event->getPackage()->getName() == 'anime-db/catalog-bundle') {
            $this->copyTemplates();
        }
    }

    protected function copyTemplates()
    {
        $from = $this->kernel->locateResource('@AnimeDbCatalogBundle/Resources/views/');
        $to = $this->root_dir.'/Resources/';
        // overwrite knp menu tpl
        $this->fs->copy($from.'knp_menu.html.twig', $to.'views/knp_menu.html.twig', true);
        // overwrite twig error tpls
        $this->fs->copy($from.'errors/error.html.twig', $to.'TwigBundle/views/Exception/error.html.twig', true);
        $this->fs->copy($from.'errors/error404.html.twig', $to.'TwigBundle/views/Exception/error404.html.twig', true);
    }
}
