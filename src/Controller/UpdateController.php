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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * System update
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class UpdateController extends BaseController
{
    /**
     * Message identifies the end of the update
     *
     * @var string
     */
    const END_MESSAGE = 'Updating the application has been completed';

    /**
     * Link to guide by update the application on Windows XP
     * 
     * @var string
     */
    const GUIDE_LINK = '/guide/general/update.html#update-win-xp';

    /**
     * Update page
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $response = $this->getCacheTimeKeeper()->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        // update for Windows XP does not work
        $can_update = strpos(php_uname('v'), 'Windows XP') === false;

        $plugin = $request->request->get('plugin');
        $action = $this->getAction($plugin);

        // delete or install package
        switch ($action) {
            case 'delete':
                $this->get('anime_db.manipulator.composer')->removePackage($plugin['delete']);
                $plugin = $this->getPlugin('plugin/'.$plugin['delete'].'/');
                break;
            case 'install':
                $this->get('anime_db.manipulator.composer')
                    ->addPackage($plugin['install']['package'], $plugin['install']['version']);
                $plugin = $this->getPlugin('plugin/'.$plugin['install']['package'].'/');
                break;
        }

        return $this->render('AnimeDbCatalogBundle:Update:index.html.twig', [
            'can_update' => $can_update,
            'doc' => !$can_update ? $this->get('anime_db.api.client')->getSiteUrl(self::GUIDE_LINK) : '',
            'referer' => $request->headers->get('referer'),
            'plugin' => $action ? $plugin : [],
            'action' => $action
        ], $response);
    }

    /**
     * Get action
     *
     * @param array $plugin
     *
     * @return string
     */
    protected function getAction($plugin)
    {
        if (!$plugin) {
            return '';
        } elseif (!empty($plugin['delete'])) {
            return 'delete';
        } elseif (!empty($plugin['install'])) {
            return 'install';
        }
        return '';
    }

    /**
     * Get plugin
     *
     * @param string $plugin
     *
     * @return array|null
     */
    protected function getPlugin($plugin)
    {
        try {
            list($vendor, $package) = explode('/', $plugin);
            return $this->get('anime_db.api.client')->getPlugin($vendor, $package);
        } catch (\RuntimeException $e) {
            return null;
        }
    }

    /**
     * Execute update application
     *
     * @return Response
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
