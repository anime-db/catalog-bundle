<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;

/**
 * Form view sorter
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ViewSorter
{
    /**
     * Collator
     *
     * @var \Collator
     */
    protected $collator;

    /**
     * Construct
     *
     * @param string $locale
     */
    public function __construct($locale)
    {
        $this->collator = new \Collator($locale);
    }

    /**
     * Sort choice
     *
     * @param \Symfony\Component\Form\FormView $choice
     */
    public function choice(FormView $choice)
    {
        $collator = $this->collator;
        if ($choice->vars['expanded']) {
            usort($choice->children, function (FormView $a, FormView $b) use ($collator) {
                return $collator->compare($a->vars['label'], $b->vars['label']);
            });
        } else {
            usort($choice->vars['choices'], function (ChoiceView $a, ChoiceView $b) use ($collator) {
                return $collator->compare($a->label, $b->label);
            });
        }
    }
}
