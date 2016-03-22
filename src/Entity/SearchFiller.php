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

use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\FillerInterface;
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
     * @Assert\NotBlank()
     * @Assert\Url()
     *
     * @var string
     */
    protected $url = '';

    /**
     * @var FillerInterface|null
     */
    protected $filler;

    /**
     * @var Chain
     */
    protected $chain;

    /**
     * @param Chain $chain
     */
    public function __construct(Chain $chain)
    {
        $this->chain = $chain;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return SearchFiller
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return FillerInterface|null
     */
    public function getFiller()
    {
        return $this->filler;
    }

    /**
     * @param ExecutionContextInterface $context
     */
    public function isUrlSupported(ExecutionContextInterface $context)
    {
        /* @var $plugin FillerInterface */
        foreach ($this->chain->getPlugins() as $plugin) {
            if ($plugin->isSupportedUrl($this->url)) {
                $this->filler = $plugin;
                return;
            }
        }

        $context
            ->buildViolation('No fillers that would support this URL')
            ->atPath('url')
            ->addViolation();
    }
}
