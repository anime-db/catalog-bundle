<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form\Notice;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Change the list notices form
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form\Notice
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Change extends AbstractType
{
    /**
     * Form name
     *
     * @var string
     */
    const NAME = 'anime_db_catalog_notices_change';

    /**
     * Action set status shown
     *
     * @var string
     */
    const ACTION_SET_STATUS_SHOWN = 'status_shown';

    /**
     * Action set status closed
     *
     * @var string
     */
    const ACTION_SET_STATUS_CLOSED = 'status_closed';

    /**
     * Action remove
     *
     * @var string
     */
    const ACTION_REMOVE = 'remove';

    /**
     * List ids
     *
     * @var array
     */
    protected $ids = [];

    /**
     * Construct
     *
     * @param array $notices
     */
    public function __construct(array $notices)
    {
        /* @var $notice \AnimeDb\Bundle\AppBundle\Entity\Notice */
        foreach ($notices as $notice) {
            $this->ids[] = $notice->getId();
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'choice', [
                'choices' => $this->ids,
                'required' => false,
                'expanded' => true,
                'multiple' => true
            ])
            ->add('action', 'choice', [
                'choices' => [
                    self::ACTION_SET_STATUS_SHOWN => 'Set status Shown',
                    self::ACTION_SET_STATUS_CLOSED => 'Set status Closed',
                    self::ACTION_REMOVE => 'Remove'
                ]
            ]);
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return self::NAME;
    }
}
