<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Controller;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Search as SearchFrom;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\SearchFiller as SearchFillerForm;
use AnimeDb\Bundle\CatalogBundle\Entity\SearchFiller;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use AnimeDb\Bundle\CatalogBundle\Plugin\Chain;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Chain as ChainFiller;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain as ChainSearch;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\FillerInterface;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\SearchInterface;
use AnimeDb\Bundle\CatalogBundle\Plugin\PluginInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fill.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class FillController extends BaseController
{
    /**
     * Create new item from source fill.
     *
     * @param string $plugin
     * @param Request $request
     *
     * @return Response
     */
    public function fillerAction($plugin, Request $request)
    {
        /* @var $response Response */
        $response = $this->getCacheTimeKeeper()->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        /* @var $chain ChainFiller */
        $chain = $this->get('anime_db.plugin.filler');
        /* @var $filler FillerInterface */
        if (!($filler = $chain->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }

        /* @var $form Form */
        $form = $this->createForm($filler->getForm());

        $fill_form = null;
        $form->handleRequest($request);
        if ($form->isValid()) {
            $item = $filler->fill($form->getData());
            if (!($item instanceof Item)) {
                $form->addError(new FormError('Can`t get content from the specified source'));
            } else {
                $fill_form = $this->createForm('entity_item', $item)->createView();
            }
        }

        return $this->render('AnimeDbCatalogBundle:Fill:filler.html.twig', [
            'plugin' => $plugin,
            'plugin_name' => $filler->getTitle(),
            'form' => $form->createView(),
            'fill_form' => $fill_form,
        ], $response);
    }

    /**
     * Search source fill for item.
     *
     * @param string $plugin
     * @param Request $request
     *
     * @return Response
     */
    public function searchAction($plugin, Request $request)
    {
        $response = $this->getCacheTimeKeeper()->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        /* @var $search SearchInterface */
        if (!($search = $this->get('anime_db.plugin.search_fill')->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }

        /* @var $form Form */
        $form = $this->createForm($search->getForm());

        $list = [];
        $form->handleRequest($request);
        if ($form->isValid()) {
            $list = $search->search($form->getData());
        }

        // full page or hinclude
        if ($request->get('hinclude', 0)) {
            $tpl = 'AnimeDbCatalogBundle:Fill:search_hinclude.html.twig';
        } else {
            $tpl = 'AnimeDbCatalogBundle:Fill:search.html.twig';
        }

        return $this->render($tpl, [
            'plugin' => $plugin,
            'plugin_name' => $search->getTitle(),
            'list' => $list,
            'form' => $form->createView(),
        ], $response);
    }

    /**
     * Search source fill for item.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function searchInAllAction(Request $request)
    {
        /* @var $chain ChainSearch */
        $chain = $this->get('anime_db.plugin.search_fill');
        $response = $this->getResponseFromChain($chain);

        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        /* @var $form Form */
        $form = $this->createForm(new SearchFrom());
        $form->handleRequest($request);

        return $this->render('AnimeDbCatalogBundle:Fill:search_in_all.html.twig', [
            'plugins' => $chain->getPlugins(),
            'form' => $form->createView(),
        ], $response);
    }

    /**
     * Search filler by URL for fill item.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function searchFillerAction(Request $request)
    {
        /* @var $chain ChainFiller */
        $chain = $this->get('anime_db.plugin.filler');
        $response = $this->getResponseFromChain($chain);

        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        $entity = new SearchFiller($chain);
        /* @var $form Form */
        $form = $this->createForm(new SearchFillerForm(), $entity)->handleRequest($request);
        if ($form->isValid()) {
            return $this->redirect($entity->getFiller()->getLinkForFill($entity->getUrl()));
        }

        return $this->render('AnimeDbCatalogBundle:Fill:search_filler.html.twig', [
            'form' => $form->createView(),
        ], $response);
    }

    /**
     * Get response from plugins chain.
     *
     * @param Chain $chain
     *
     * @return Response
     */
    protected function getResponseFromChain(Chain $chain)
    {
        if (!$chain->getPlugins()) {
            throw $this->createNotFoundException('No any plugins');
        }

        $names = '';
        /* @var $plugin PluginInterface */
        foreach ($chain->getPlugins() as $plugin) {
            $names .= '|'.$plugin->getName();
        }

        return $this
            ->getCacheTimeKeeper()
            ->getResponse()
            ->setEtag(md5($names));
    }
}
