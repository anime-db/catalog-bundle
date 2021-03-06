<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search;

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;
use Knp\Menu\ItemInterface;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Search as SearchForm;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Filler as FillerForm;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\FillerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use AnimeDb\Bundle\CatalogBundle\Entity\Item as EntityItem;

/**
 * Plugin search.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Search extends Plugin implements SearchInterface
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * @var FillerInterface
     */
    protected $filler;

    /**
     * Build menu for plugin.
     *
     * @param ItemInterface $item
     *
     * @return ItemInterface
     */
    public function buildMenu(ItemInterface $item)
    {
        return $item->addChild($this->getTitle(), [
            'route' => 'fill_search',
            'routeParameters' => ['plugin' => $this->getName()],
        ]);
    }

    /**
     * @param Router $router
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @return SearchForm
     */
    public function getForm()
    {
        return new SearchForm();
    }

    /**
     * @param FillerInterface $filler
     */
    public function setFiller(FillerInterface $filler)
    {
        $this->filler = $filler;
    }

    /**
     * @return FillerInterface
     */
    public function getFiller()
    {
        return $this->filler;
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    public function getLinkForFill($data)
    {
        if ($this->filler instanceof FillerInterface) {
            return $this->filler->getLinkForFill($data);
        } else {
            return $this->router->generate(
                'fill_filler',
                [
                    'plugin' => $this->getName(),
                    FillerForm::FORM_NAME => ['url' => $data],
                ]
            );
        }
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getLinkForSearch($name)
    {
        return $this->router->generate('fill_search', [
            'plugin' => $this->getName(),
            $this->getForm()->getName() => ['name' => $name],
        ]);
    }

    /**
     * Try search item by name and fill it if can.
     *
     * @param string $name
     *
     * @return EntityItem|null
     */
    public function getCatalogItem($name)
    {
        if (!($this->getFiller() instanceof FillerInterface)) {
            return;
        }

        try {
            $list = $this->search(['name' => $name]);
            if (count($list) == 1) {
                return $this->getFiller()->fillFromSearchResult(array_pop($list));
            }
        } catch (\Exception $e) {
        } // is not a critical error

        return;
    }
}
