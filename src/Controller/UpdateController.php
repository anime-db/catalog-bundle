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
    const END_MESSAGE = 'Updating the application has been completed';

    /**
     * Link to documentation by update the application on Windows XP
     * 
     * @var string
     */
    const DOC_LINK = 'http://anime-db.org/%locale%/guide/general/update.html#update-win-xp';

    /**
     * Default documentation locale
     * 
     * @var string
     */
    const DEFAULT_DOC_LOCALE = 'en';

    /**
     * Supported documentation locale
     *
     * @var array
     */
    protected $support_locales = ['en', 'ru'];

    /**
     * Update page
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $response = $this->get('cache_time_keeper')->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        // update for Windows XP does not work
        $can_update = strpos(php_uname('v'), 'Windows XP') === false;

        // delete or install package
        $action = false;
        if ($plugin = $request->request->get('plugin')) {
            /* @var $manipulator \AnimeDb\Bundle\AnimeDbBundle\Manipulator\Composer */
            $manipulator = $this->get('anime_db.manipulator.composer');

            if (!empty($plugin['delete'])) {
                $manipulator->removePackage($plugin['delete']);
                $action = 'delete';
            } elseif (!empty($plugin['install'])) {
                $manipulator->addPackage($plugin['install']['package'], $plugin['install']['version']);
                $action = 'install';
            }

            if ($action) {
                // get info about plugin
                if ($action == 'install') {
                    $api_request = 'plugin/'.$plugin['install']['package'].'/';
                } else {
                    $api_request = 'plugin/'.$plugin['delete'].'/';
                }
                $api_response = $this->container->get('anime_db.api_client')->get($api_request);
                $plugin = false;
                if ($api_response->isSuccessful()) {
                    $plugin = json_decode($api_response->getBody(true), true);
                }
            }
        }

        return $this->render('AnimeDbCatalogBundle:Update:index.html.twig', [
            'can_update' => $can_update,
            'doc' => !$can_update ? $this->getDocLink($request->getLocale()) : '',
            'referer' => $request->headers->get('referer'),
            'plugin' => $plugin,
            'action' => $action
        ], $response);
    }

    /**
     * Return documentation link
     *
     * @param string $locale
     *
     * @return string
     */
    protected function getDocLink($locale)
    {
        $locale = substr($locale, 0, 2);
        $locale = in_array($locale, $this->support_locales) ? $locale : self::DEFAULT_DOC_LOCALE;
        return str_replace('%locale%', $locale, self::DOC_LINK);
    }

    /**
     * Execute update application
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function executeAction()
    {
        // update for Windows XP does not work
        if (strpos(php_uname('v'), 'Windows XP') !== false) {
            $this->redirect($this->generateUrl('update'));
        }

        // execute update
        file_put_contents($this->container->getParameter('kernel.root_dir').'/../web/update.log', '');
        $this->get('anime_db.command')->send('php app/console animedb:update --env=prod >web/update.log 2>&1');

        return $this->render('AnimeDbCatalogBundle:Update:execute.html.twig', [
            'log_file' => '/update.log',
            'end_message' => self::END_MESSAGE
        ]);
    }
}
