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
use Doctrine\ORM\EntityManagerInterface;

/**
 * Filter notices form
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form\Type\Notice
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Filter extends AbstractType
{
    /**
     * @var string
     */
    const NAME = 'anime_db_catalog_notices_filter';

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
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
            ])
            ->setMethod('GET');
    }

    /**
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
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}
