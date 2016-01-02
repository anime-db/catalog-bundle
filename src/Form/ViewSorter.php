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
     * Sort choice
     *
     * @param \Symfony\Component\Form\FormView $choice
     */
    public function choice(FormView $choice)
    {
        // need use intl Collator::compare
        if ($choice->vars['compound']) {
            usort($choice->children, function (FormView $a, FormView $b) {
                $a = $a->vars['label']?:$a->vars['value'];
                $b = $b->vars['label']?:$b->vars['value'];
                return $a == $b ? 0 : ($a > $b ? 1 : -1);
            });
        } else {
            usort($choice->vars['choices'], function (ChoiceView $a, ChoiceView $b) {
                $a = $a->label?:$a->value;
                $b = $b->label?:$b->value;
                return $a == $b ? 0 : ($a > $b ? 1 : -1);
            });
        }
    }
}
