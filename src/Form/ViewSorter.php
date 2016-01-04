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
     * @var \Collator|null
     */
    protected $collator = null;

    /**
     * Construct
     *
     * @param string $locale
     */
    public function __construct($locale)
    {
        if (extension_loaded('intl')) {
            $this->collator = new \Collator($locale);
        }
    }

    /**
     * Sort choice
     *
     * @param \Symfony\Component\Form\FormView $choice
     */
    public function choice(FormView $choice)
    {
        $that = $this;
        if ($choice->vars['compound']) {
            usort($choice->children, function (FormView $a, FormView $b) use ($that) {
                return $that->compare($a->vars['label']?:$a->vars['value'], $b->vars['label']?:$b->vars['value']);
            });
        } else {
            usort($choice->vars['choices'], function (ChoiceView $a, ChoiceView $b) use ($that) {
                return $that->compare($a->label?:$a->value, $b->label?:$b->value);
            });
        }
    }

    /**
     * @param string $a
     * @param string $b
     *
     * @return integer
     */
    public function compare($a, $b)
    {
        if ($this->collator instanceof \Collator) {
            return $this->collator->compare($a, $b);
        } else {
            return $a == $b ? 0 : ($a > $b ? 1 : -1);
        }
    }
}
