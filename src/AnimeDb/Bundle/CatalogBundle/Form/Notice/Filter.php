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
use AnimeDb\Bundle\AppBundle\Entity\Notice;
use AnimeDb\Bundle\CatalogBundle\Event\Listener\ScanStorage;

/**
 * Filter notices form
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form\Notice
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Filter extends AbstractType
{
    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('type', 'choice', [
                'choices' => $this->getNormalLabels([
                    Notice::DEFAULT_TYPE,
                    ScanStorage::NOTICE_TYPE_ADDED_NEW_ITEM,
                    ScanStorage::NOTICE_TYPE_DETECTED_FILES_FOR_NEW_ITEM,
                    ScanStorage::NOTICE_TYPE_ITEM_FILES_NOT_FOUND,
                    ScanStorage::NOTICE_TYPE_UPDATE_ITEM_FILES
                ]),
                'empty_value' => 'Show all',
                'required' => false
            ]);
    }

    /**
     * Get normal labels
     *
     * @param array $labels
     *
     * @return array
     */
    protected function getNormalLabels(array $labels)
    {
        $choices = [];
        foreach ($labels as $label) {
            $choices[$label] = ucfirst(str_replace(['-', '_'], ' ', $label));
        }
        return $choices;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'anime_db_catalog_notices_filter';
    }
}