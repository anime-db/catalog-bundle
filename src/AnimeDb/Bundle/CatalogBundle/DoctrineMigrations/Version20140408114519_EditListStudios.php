<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\DBAL\Schema\Schema;
use AnimeDb\Bundle\CatalogBundle\Entity\Studio;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140408114519_EditListStudios extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * Entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Add studios
     *
     * @var array
     */
    protected $add = [
        'Actas',
        'OB Planning',
        'Studio Rikka',
        'Group TAC',
        'Brains Base',
        'Studio Nue',
        'Anima',
        'Hoods Entertainment',
        'Palm Studio',
        'MAPPA',
        'Cammot',
        'Fifth Avenue',
        'NAZ',
        'Pierrot Plus'
    ];

    /**
     * Rename studios
     *
     * @var array
     */
    protected $rename = [
        'Xebec' => 'XEBEC',
        'Ufotable' => 'ufotable',
        'Shaft' => 'SHAFT',
        'A-1 Pictures' => 'A-1 Pictures Inc.',
        'Imagin' => 'IMAGIN',
        'Feel' => 'feel.',
        'Animax Entertainment' => 'Animax',
        'A.C.G.T' => 'A.C.G.T.',
        'Zexcs' => 'ZEXCS'
    ];
    /**
     * Set container
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::up()
     */
    public function up(Schema $schema)
    {
        $rep = $this->em->getRepository('AnimeDbCatalogBundle:Studio');

        // rename studios
        /* @var $studio \AnimeDb\Bundle\CatalogBundle\Entity\Studio */
        foreach ($this->rename as $from => $to) {
            $studio = $rep->findOneByName($from);
            $studio->setName($to);
            $this->em->persist($studio);
        }

        // add new studios
        foreach ($this->add as $name) {
            $studio = new Studio();
            $studio->setName($name);
            $this->em->persist($studio);
        }
        $this->em->flush();
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::down()
     */
    public function down(Schema $schema)
    {
        $rep = $this->em->getRepository('AnimeDbCatalogBundle:Studio');

        // rename studios
        /* @var $studio \AnimeDb\Bundle\CatalogBundle\Entity\Studio */
        foreach ($this->rename as $from => $to) {
            $studio = $rep->findOneByName($to);
            $studio->setName($from);
            $this->em->persist($studio);
        }

        // remove studios
        foreach ($this->add as $name) {
            $this->em->remove($rep->findOneByName($name));
        }
    }
}
