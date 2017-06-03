<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler;

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;
use Knp\Menu\ItemInterface;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Filler as FillerForm;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Item as ItemSearch;

/**
 * Plugin filler.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Filler extends Plugin implements FillerInterface
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * @param ItemInterface $item
     *
     * @return ItemInterface
     */
    public function buildMenu(ItemInterface $item)
    {
        return $item->addChild($this->getTitle(), [
            'route' => 'fill_filler',
            'routeParameters' => ['plugin' => $this->getName()],
        ]);
    }

    /**
     * @return Filler
     */
    public function getForm()
    {
        return new FillerForm();
    }

    /**
     * @param Router $router
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @throws \LogicException
     *
     * @param mixed $data
     *
     * @return string
     */
    public function getLinkForFill($data)
    {
        if (!($this->router instanceof Router)) {
            throw new \LogicException('Link cannot be built without a Router');
        }

        return $this->router->generate(
            'fill_filler',
            [
                'plugin' => $this->getName(),
                $this->getForm()->getName() => ['url' => $data],
            ]
        );
    }

    /**
     * Fill from search result.
     *
     * @param ItemSearch $item
     *
     * @return Item|null
     */
    public function fillFromSearchResult(ItemSearch $item)
    {
        $query = parse_url($item->getLink(), PHP_URL_QUERY);
        parse_str($query, $query);
        if (empty($query[$this->getForm()->getName()])) {
            return;
        }

        return $this->fill($query[$this->getForm()->getName()]);
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public function isSupportedUrl($url)
    {
        return false;
    }
}
