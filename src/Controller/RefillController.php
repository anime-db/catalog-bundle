<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Controller;

use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use AnimeDb\Bundle\CatalogBundle\Entity\Source;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\RefillerInterface;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Refiller\DateEnd as DateEndForm;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Refiller\DatePremiere as DatePremiereForm;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Refiller\Duration as DurationForm;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Refiller\Episodes as EpisodesForm;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Refiller\EpisodesNumber as EpisodesNumberForm;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Refiller\FileInfo as FileInfoForm;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Refiller\Images as ImagesForm;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Refiller\Names as NamesForm;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Refiller\Sources as SourcesForm;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Refiller\Summary as SummaryForm;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Refiller\Translate as TranslateForm;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Item as ItemRefiller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Refill.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class RefillController extends BaseController
{
    /**
     * Refill item.
     *
     * @param string $plugin
     * @param string $field
     * @param Request $request
     *
     * @return Response
     */
    public function refillAction($plugin, $field, Request $request)
    {
        /* @var $refiller RefillerInterface */
        if (!($refiller = $this->get('anime_db.plugin.refiller')->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }
        $item = $this->createForm('entity_item', new Item())
            ->handleRequest($request)
            ->getData();

        $form = $this->getForm($field, clone $item, $refiller->refill($item, $field));

        return $this->render('AnimeDbCatalogBundle:Refill:refill.html.twig', [
            'field' => $field,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Search for refill.
     *
     * @param string $plugin
     * @param string $field
     * @param Request $request
     *
     * @return Response
     */
    public function searchAction($plugin, $field, Request $request)
    {
        /* @var $refiller RefillerInterface */
        if (!($refiller = $this->get('anime_db.plugin.refiller')->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }
        $item = $this->createForm('entity_item', new Item())
            ->handleRequest($request)
            ->getData();

        $result = [];
        if ($refiller->isCanSearch($item, $field)) {
            $result = $refiller->search($item, $field);
            /* @var $search_item ItemRefiller */
            foreach ($result as $key => $search_item) {
                $result[$key] = [
                    'name' => $search_item->getName(),
                    'image' => $search_item->getImage(),
                    'description' => $search_item->getDescription(),
                    'source' => $search_item->getSource(),
                    'link' => $this->generateUrl('refiller_search_fill', [
                        'plugin' => $plugin,
                        'field' => $field,
                        'id' => $item->getId(),
                        'data' => $search_item->getData(),
                        'source' => $search_item->getSource(),
                    ]),
                ];
            }
        }

        return $this->render('AnimeDbCatalogBundle:Refill:search.html.twig', [
            'result' => $result,
        ]);
    }

    /**
     * Refill item from search result.
     *
     * @param string $plugin
     * @param string $field
     * @param Request $request
     *
     * @return Response
     */
    public function fillFromSearchAction($plugin, $field, Request $request)
    {
        /* @var $refiller RefillerInterface */
        if (!($refiller = $this->get('anime_db.plugin.refiller')->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }
        $item = $this->createForm('entity_item', new Item())
            ->handleRequest($request)
            ->getData();

        $form = $this->getForm($field, clone $item, $refiller->refillFromSearchResult($item, $field, $request->get('data')));

        return $this->render('AnimeDbCatalogBundle:Refill:refill.html.twig', [
            'field' => $field,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Get form for field.
     *
     * @param string $field
     * @param Item $item_origin
     * @param Item $item_fill
     *
     * @return Form
     */
    protected function getForm($field, Item $item_origin, Item $item_fill)
    {
        switch ($field) {
            case RefillerInterface::FIELD_DATE_END:
                $form = new DateEndForm();
                $data = ['date_end' => $item_fill->getDateEnd()];
                break;
            case RefillerInterface::FIELD_DATE_PREMIERE:
                $form = new DatePremiereForm();
                $data = ['date_premiere' => $item_fill->getDatePremiere()];
                break;
            case RefillerInterface::FIELD_DURATION:
                $form = new DurationForm();
                $data = ['duration' => $item_fill->getDuration()];
                break;
            case RefillerInterface::FIELD_EPISODES:
                $form = new EpisodesForm();
                $data = ['episodes' => $item_fill->getEpisodes()];
                break;
            case RefillerInterface::FIELD_EPISODES_NUMBER:
                $form = new EpisodesNumberForm();
                $data = ['episodes_number' => $item_fill->getEpisodesNumber()];
                break;
            case RefillerInterface::FIELD_FILE_INFO:
                $form = new FileInfoForm();
                $data = ['file_info' => $item_fill->getFileInfo()];
                break;
            case RefillerInterface::FIELD_GENRES:
                $form = $this->get('anime_db.form.type.refill.gengres');
                $data = ['genres' => $item_fill->getGenres()];
                break;
            case RefillerInterface::FIELD_IMAGES:
                $form = new ImagesForm();
                $data = ['images' => $item_fill->getImages()];
                break;
            case RefillerInterface::FIELD_COUNTRY:
                $form = $this->get('anime_db.form.type.refill.country');
                $data = ['country' => $item_fill->getCountry()];
                break;
            case RefillerInterface::FIELD_NAMES:
                $form = new NamesForm();
                $data = ['names' => $item_fill->getNames()];
                break;
            case RefillerInterface::FIELD_SOURCES:
                $form = new SourcesForm();
                $data = ['sources' => $item_fill->getSources()];
                break;
            case RefillerInterface::FIELD_SUMMARY:
                $form = new SummaryForm();
                $data = ['summary' => $item_fill->getSummary()];
                break;
            case RefillerInterface::FIELD_TRANSLATE:
                $form = new TranslateForm();
                $data = ['translate' => $item_fill->getTranslate()];
                break;
            case RefillerInterface::FIELD_STUDIO:
                $form = $this->get('anime_db.form.type.refill.studio');
                $data = ['studio' => $item_fill->getStudio()];
                break;
            default:
                throw $this->createNotFoundException('Field \''.$field.'\' is not supported');
        }
        // search new source link
        /* @var $sources_origin Source[] */
        $sources_origin = array_reverse($item_origin->getSources()->toArray());
        /* @var $sources_fill Source[] */
        $sources_fill = array_reverse($item_fill->getSources()->toArray());
        foreach ($sources_fill as $source_fill) {
            // sources is already added
            foreach ($sources_origin as $source_origin) {
                if ($source_fill->getUrl() == $source_origin->getUrl()) {
                    continue 2;
                }
            }
            $data['source'] = $source_fill->getUrl();
            break;
        }

        return $this->createForm($form, $data);
    }
}
