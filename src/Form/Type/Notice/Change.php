<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form\Type\Notice;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Change the list notices form.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Change extends AbstractType
{
    /**
     * Form name.
     *
     * @var string
     */
    const NAME = 'notices_change';

    /**
     * Action set status shown.
     *
     * @var string
     */
    const ACTION_SET_STATUS_SHOWN = 'status_shown';

    /**
     * Action set status closed.
     *
     * @var string
     */
    const ACTION_SET_STATUS_CLOSED = 'status_closed';

    /**
     * Action remove.
     *
     * @var string
     */
    const ACTION_REMOVE = 'remove';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('notices', 'entity', [
                'class' => 'AnimeDbAppBundle:Notice',
                'property' => 'id',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('action', 'choice', [
                'choices' => [
                    self::ACTION_SET_STATUS_SHOWN => 'Set status Shown',
                    self::ACTION_SET_STATUS_CLOSED => 'Set status Closed',
                    self::ACTION_REMOVE => 'Remove',
                ],
            ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}
