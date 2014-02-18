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
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * System update
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class UpdateController extends Controller
{
    /**
     * Message identifies the end of the update
     *
     * @var string
     */
    const END_MESSAGE = '\r?\nUpdating the application has been completed\r?\n';

    /**
     * Update page
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
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

        if ($request->request->get('confirm')) {
            // delete or install package
            if ($plugin = $request->request->get('plugin')) {
                $root = $this->container->getParameter('kernel.root_dir').'/../';
                $composer = json_decode(file_get_contents($root.'composer.json'), true);

                if (!empty($plugin['delete'])) {
                    unset($composer['require'][$plugin['delete']]);
                } elseif (!empty($plugin['install'])) {
                    $composer['require'][$plugin['install']['package']] = $plugin['install']['version'];
                }

                // lock file is longer not relevant
                if (file_exists($root.'composer.lock')) {
                    unlink($root.'composer.lock');
                }
                $composer = json_encode($composer, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
                file_put_contents($root.'composer.json', $composer);
            }

            // execute update
            $this->get('anime_db.command')
                ->exec('php -d memory_limit=-1 -f app/console animedb:update >web/update.log');
        }

        return $this->render('AnimeDbCatalogBundle:Update:index.html.twig', [
            'confirmed' => $request->request->get('confirm'),
            'log_file' => '/update.log',
            'end_message' => self::END_MESSAGE
        ], $response);
    }
}