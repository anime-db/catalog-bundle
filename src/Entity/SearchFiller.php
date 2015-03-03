<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Chain;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Search filler
 *
 * @Assert\Callback(methods={"isUrlSupported"})
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class SearchFiller
{
    /**
     * URL
     *
     * @Assert\NotBlank()
     * @Assert\Url()
     *
     * @var string
     */
    protected $url = '';

    /**
     * Filler
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler|null
     */
    protected $filler;

    /**
     * Chain
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Chain
     */
    protected $chain;

    /**
     * Construct
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Chain $chain
     */
    public function __construct(Chain $chain)
    {
        $this->chain = $chain;
    }

    /**
     * Get URL
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set URL
     *
     * @param string $url
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\SearchFiller
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get filler
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler|null
     */
    public function getFiller()
    {
        return $this->filler;
    }

    /**
     * Is URL supported
     *
     * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
     */
    public function isUrlSupported(ExecutionContextInterface $context)
    {
        /* @var $plugin \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler */
        foreach ($this->chain->getPlugins() as $plugin) {
            if ($plugin->isSupportedUrl($this->url)) {
                $this->filler = $plugin;
                return;
            }
        }

        $context->buildViolation('No fillers that would support this URL')
            ->atPath('url')
            ->addViolation();
    }
}
