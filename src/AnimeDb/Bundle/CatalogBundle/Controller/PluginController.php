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
use Symfony\Component\HttpFoundation\Response;
use Guzzle\Http\Client;

/**
 * Plugin
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class PluginController extends Controller
{
    /**
     * API server host
     *
     * @var string
     */
    const API_HOST = 'http://anime-db.org/';

    /**
     * API version
     *
     * @var string
     */
    const API_VERSION = 1;

    /**
     * API default locale
     *
     * @var string
     */
    const API_DEFAULT_LOCALE = 'en';

    /**
     * List of available locales
     *
     * @var array
     */
    protected $locales = ['ru', 'en'];

    /**
     * Installed plugins
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function installedAction(Request $request)
    {
        $response = new Response();
        // caching
        if ($last_update = $this->container->getParameter('last_update')) {
            $response->setPublic();
            $response->setLastModified(new \DateTime($last_update));

            // response was not modified for this request
            if ($response->isNotModified($request)) {
                return $response;
            }
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function storeAction()
    {
        $locale = substr($this->container->getParameter('locale'), 0, 2);
        $locale = in_array($locale, $this->locales) ? $locale : self::API_DEFAULT_LOCALE;
        $path = 'api/v'.self::API_VERSION.'/'.$locale.'/plugin/';
        $client = new Client(self::API_HOST);
        /* @var $response \Guzzle\Http\Message\Response */
        $response = $client->get($path)->send();

        if ($response->isSuccessful()) {
            $data = json_decode($response->getBody(true), true);
            $plugins = [];
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
        }

        return $this->render('AnimeDbCatalogBundle:Plugin:store.html.twig', [
            'plugins' => $plugins
        ]);
    }
}