<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form\Type\Notice;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AnimeDb\Bundle\AppBundle\Entity\Notice;
use Doctrine\ORM\EntityManager;

/**
 * Filter notices form
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form\Type\Notice
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Filter extends AbstractType
{
    /**
     * Form name
     *
     * @var string
     */
    const NAME = 'anime_db_catalog_notices_filter';

    /**
     * Entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Construct
     *
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $types = [Notice::DEFAULT_TYPE];

        // add user-defined types
        $result = $this->em->createQueryBuilder()
            ->select('n.type')
            ->from('AnimeDbAppBundle:Notice', 'n')
            ->where('n.type IS NOT NULL')
            ->groupBy('n.type')
            ->getQuery()
            ->getResult();
        foreach ($result as $row) {
            $types[] = $row['type'];
        }

        $builder
            ->setMethod('GET')
            ->add('status', 'choice', [
                'choices' => [
                    Notice::STATUS_CREATED => 'New',
                    Notice::STATUS_SHOWN => 'Shown',
                    Notice::STATUS_CLOSED => 'Closed'
                ],
                'required' => false
            ])
            ->add('type', 'choice', [
                'choices' => $this->getNormalLabels($types),
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
        return self::NAME;
    }
}