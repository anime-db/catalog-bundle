<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Entity\Settings;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * General Settings.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class General
{
    /**
     * @Assert\Type(type="bool", message="The value {{ value }} is not a valid {{ type }}.")
     *
     * @var string
     */
    protected $task_scheduler = true;

    /**
     * Locale.
     *
     * @Assert\Locale
     *
     * @var string
     */
    protected $locale = '';

    /**
     * Plugin default search to fill.
     *
     * @var string
     */
    protected $default_search = '';

    /**
     * @return string
     */
    public function getTaskScheduler()
    {
        return $this->task_scheduler;
    }

    /**
     * @param string $task_scheduler
     *
     * @return General
     */
    public function setTaskScheduler($task_scheduler)
    {
        $this->task_scheduler = $task_scheduler;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return General
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultSearch()
    {
        return $this->default_search;
    }

    /**
     * @param string $default_search
     *
     * @return General
     */
    public function setDefaultSearch($default_search)
    {
        $this->default_search = $default_search;

        return $this;
    }
}
