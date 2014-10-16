<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Plugin
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class PluginController extends Controller
{
    /**
     * Cache lifetime 1 day
     *
     * @var integer
     */
    const CACHE_LIFETIME = 86400;

    /**
     * Installed plugins
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function installedAction(Request $request)
    {
        $response = $this->get('cache_time_keeper')->getResponse('AnimeDbAppBundle:Plugin');
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $this->getDoctrine()->getRepository('AnimeDbAppBundle:Plugin');
        return $this->render('AnimeDbCatalogBundle:Plugin:installed.html.twig', [
            'plugins' => $repository->findAll()
        ], $response);
    }

    /**
     * Store of plugins
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function storeAction(Request $request)
    {
        $response = $this->get('cache_time_keeper')->getResponse([], self::CACHE_LIFETIME);
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        $plugins = [];
        $data = $this->container->get('anime_db.api.client')->getPlugins();
        foreach ($data['plugins'] as $plugin) {
            $plugins[$plugin['name']] = $plugin;
            $plugins[$plugin['name']]['installed'] = false;
        }

        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $this->getDoctrine()->getRepository('AnimeDbAppBundle:Plugin');
        /* @var $plugin \AnimeDb\Bundle\AppBundle\Entity\Plugin */
        foreach ($repository->findAll() as $plugin) {
            $plugins[$plugin->getName()]['installed'] = true;
        }

        return $this->render('AnimeDbCatalogBundle:Plugin:store.html.twig', [
            'plugins' => $plugins
        ], $response);
    }
}
