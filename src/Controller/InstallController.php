<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use AnimeDb\Bundle\CatalogBundle\Repository\Storage as StorageRepository;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Entity\Storage as StorageForm;
use AnimeDb\Bundle\AppBundle\Util\Filesystem;
use AnimeDb\Bundle\CatalogBundle\Event\Install\App as AppInstall;
use AnimeDb\Bundle\CatalogBundle\Event\Install\Samples as SamplesInstall;
use AnimeDb\Bundle\CatalogBundle\Event\Install\StoreEvents;
use Symfony\Component\HttpFoundation\Response;

/**
 * Installation controller.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class InstallController extends BaseController
{
    /**
     * Link to guide, how scan the storage.
     *
     * @var string
     */
    const GUIDE_LINK_SCAN = '/guide/storage/scan.html';

    /**
     * Link to guide, how to start work.
     *
     * @var string
     */
    const GUIDE_LINK_START = '/guide/start.html';

    /**
     * Home (Stap #1).
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        // app already installed
        if ($this->container->getParameter('anime_db.catalog.installed')) {
            return $this->redirect($this->generateUrl('home'));
        }

        $response = $this->getCacheTimeKeeper()->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }
        $form = $this->createForm('anime_db_catalog_install_settings')->handleRequest($request);

        if ($form->isValid()) {
            // update params
            $this->get('anime_db.manipulator.parameters')
                ->set('locale', $form->getData()['locale']);
            $this->get('anime_db.manipulator.parameters')
                ->set('anime_db.catalog.default_search', $form->getData()['default_search']);
            // clear cache
            $this->get('anime_db.cache_clearer')->clear();
            // redirect to step 2
            return $this->redirect($this->generateUrl('install_add_storage'));
        }

        return $this->render('AnimeDbCatalogBundle:Install:index.html.twig', [
            'form' => $form->createView(),
        ], $response);
    }

    /**
     * Add storage (Stap #2).
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addStorageAction(Request $request)
    {
        // app already installed
        if ($this->container->getParameter('anime_db.catalog.installed')) {
            return $this->redirect($this->generateUrl('home'));
        }

        $response = $this->getCacheTimeKeeper()->getResponse('AnimeDbCatalogBundle:Storage');
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }
        // get last storage
        $storage = $this->getRepository()->getLast();
        if (!$storage) {
            $storage = new Storage();
            $storage->setPath(Filesystem::getUserHomeDir());
        }

        $form = $this->createForm(new StorageForm(), $storage)->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($storage);
            $em->flush();
            // redirect to step 3
            return $this->redirect($this->generateUrl('install_what_you_want'));
        }

        return $this->render('AnimeDbCatalogBundle:Install:add_storage.html.twig', [
            'form' => $form->createView(),
            'is_new' => !$storage->getId(),
            'guide' => $this->get('anime_db.api.client')->getSiteUrl(StorageController::GUIDE_LINK),
        ], $response);
    }

    /**
     * What you want.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function whatYouWantAction(Request $request)
    {
        // app already installed
        if ($this->container->getParameter('anime_db.catalog.installed')) {
            return $this->redirect($this->generateUrl('home'));
        }

        $response = $this->getCacheTimeKeeper()->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        if ($request->isMethod('POST')) {
            $storage = $this->getRepository()->getLast();
            $this->get('event_dispatcher')->dispatch(StoreEvents::INSTALL_SAMPLES, new SamplesInstall($storage));

            return $this->redirect($this->generateUrl('install_end_skip', ['from' => 'install_sample']));
        }

        return $this->render('AnimeDbCatalogBundle:Install:what_you_want.html.twig', [
            'guide' => $this->get('anime_db.api.client')->getSiteUrl(self::GUIDE_LINK_SCAN),
        ], $response);
    }

    /**
     * Scan storage (Stap #4).
     *
     * @param Request $request
     *
     * @return Response
     */
    public function scanAction(Request $request)
    {
        // app already installed
        if ($this->container->getParameter('anime_db.catalog.installed')) {
            return $this->redirect($this->generateUrl('home'));
        }

        $storage = $this->getRepository()->getLast();
        if (!$storage) {
            return $this->redirect('install_add_storage');
        }

        $response = $this->getCacheTimeKeeper()->getResponse($storage->getDateUpdate());

        // scan storage in background
        $this->get('anime_db.storage.scan_executor')->export($storage);

        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        return $this->render('AnimeDbCatalogBundle:Install:scan.html.twig', [
            'storage' => $storage,
        ], $response);
    }

    /**
     * End install (Stap #5).
     *
     * @param Request $request
     * @param string $from
     *
     * @return Response
     */
    public function endAction(Request $request, $from = '')
    {
        // app already installed
        if ($this->container->getParameter('anime_db.catalog.installed')) {
            return $this->redirect($this->generateUrl('home'));
        }

        $response = $this->getCacheTimeKeeper()->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        if ($request->isMethod('POST')) {
            $this->get('event_dispatcher')->dispatch(StoreEvents::INSTALL_APP, new AppInstall());

            return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('AnimeDbCatalogBundle:Install:end.html.twig', [
            'guide' => $this->get('anime_db.api.client')->getSiteUrl(self::GUIDE_LINK_START),
            'from' => $from,
        ], $response);
    }

    /**
     * @return StorageRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Storage');
    }
}
