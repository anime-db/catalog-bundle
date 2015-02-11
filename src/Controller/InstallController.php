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
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Entity\Storage as StorageForm;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Installation controller
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class InstallController extends Controller
{
    /**
     * Home
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        // app already installed
        if ($this->container->getParameter('anime_db.catalog.installed')) {
            return $this->redirect($this->generateUrl('home'));
        }

        $response = $this->get('cache_time_keeper')->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }
        $form = $this->createForm('anime_db_catalog_install_locale')
            ->handleRequest($request);

        if ($form->isValid()) {
            // update params
            $this->get('anime_db.manipulator.parameters')
                ->set('locale', $form->getData()['locale']);
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
     * Add storage
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addStorageAction(Request $request)
    {
        // app already installed
        if ($this->container->getParameter('anime_db.catalog.installed')) {
            return $this->redirect($this->generateUrl('home'));
        }

        $response = $this->get('cache_time_keeper')->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }
        $storage = new Storage();
        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new StorageForm(), $storage)
            ->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($storage);
            $em->flush();
            // redirect to step 3
            return $this->redirect($this->generateUrl('install_storage_scan', ['id' => $storage->getId()]));
        }

        return $this->render('AnimeDbCatalogBundle:Install:add_storage.html.twig', [
            'form' => $form->createView(),
        ], $response);
    }

    /**
     * Scan storage
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function scanAction(Storage $storage, Request $request)
    {
        // app already installed
        if ($this->container->getParameter('anime_db.catalog.installed')) {
            return $this->redirect($this->generateUrl('home'));
        }

        $response = $this->get('cache_time_keeper')->getResponse($storage->getDateUpdate());

        $scan_output = $this->container->getParameter('anime_db.catalog.storage.scan_output');
        $scan_output = sprintf($scan_output, $storage->getId());
        if (!is_dir($dir = pathinfo($scan_output, PATHINFO_DIRNAME))) {
            if (true !== @mkdir($dir, 0755, true)) {
                throw new IOException('Unable to create directory for logging output');
            }
        }

        // scan storage in background
        $this->get('anime_db.command')->send(sprintf(
            'php app/console animedb:scan-storage --no-ansi --export=%s %s >%s 2>&1',
            sprintf($this->container->getParameter('anime_db.catalog.storage.scan_progress'), $storage->getId()),
            $storage->getId(),
            $scan_output
        ));

        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        return $this->render('AnimeDbCatalogBundle:Install:scan.html.twig', [
            'storage' => $storage
        ], $response);
    }

    /**
     * End install
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function endAction(Request $request)
    {
        // app already installed
        if ($this->container->getParameter('anime_db.catalog.installed')) {
            return $this->redirect($this->generateUrl('home'));
        }

        $response = $this->get('cache_time_keeper')->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        return $this->render('AnimeDbCatalogBundle:Install:end.html.twig', [], $response);
    }
}
