<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Controller;

use AnimeDb\Bundle\AppBundle\Entity\Plugin;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Plugin.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class PluginController extends BaseController
{
    /**
     * Cache lifetime 1 day.
     *
     * @var int
     */
    const CACHE_LIFETIME = 86400;

    /**
     * Installed plugins.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function installedAction(Request $request)
    {
        $response = $this->getCacheTimeKeeper()->getResponse('AnimeDbAppBundle:Plugin');
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        /* @var $rep EntityRepository */
        $rep = $this->getDoctrine()->getRepository('AnimeDbAppBundle:Plugin');

        return $this->render('AnimeDbCatalogBundle:Plugin:installed.html.twig', [
            'plugins' => $rep->findAll(),
        ], $response);
    }

    /**
     * Store of plugins.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function storeAction(Request $request)
    {
        $response = $this->getCacheTimeKeeper()->getResponse([], self::CACHE_LIFETIME);
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        $plugins = [];
        $data = $this->get('anime_db.api.client')->getPlugins();
        foreach ($data['plugins'] as $plugin) {
            $plugins[$plugin['name']] = $plugin;
            $plugins[$plugin['name']]['installed'] = false;
        }

        /* @var $rep EntityRepository */
        $rep = $this->getDoctrine()->getRepository('AnimeDbAppBundle:Plugin');
        /* @var $plugin Plugin */
        foreach ($rep->findAll() as $plugin) {
            $plugins[$plugin->getName()]['installed'] = true;
        }

        return $this->render('AnimeDbCatalogBundle:Plugin:store.html.twig', [
            'plugins' => $plugins,
        ], $response);
    }
}
