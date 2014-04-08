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
use AnimeDb\Bundle\CatalogBundle\Entity\Genre;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140408113030_AddItemGenres extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * Entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Add genres
     *
     * @var array
     */
    protected $genres = [
        'Cars' => 'Машины',
        'Demons' => 'Демоны',
        'Game' => 'Игры',
        'Magic' => 'Магия',
        'Space' => 'Космос',
        'Super Power' => 'Супер сила',
        'Yuri' => 'Юри',
        'Harem' => 'Гарем',
        'Supernatural' => 'Сверхъестественное',
        'Gender Bender' => 'Смена пола'
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
        foreach ($this->genres as $en => $ru) {
            $genre = new Genre();
            $genre->setName($en)->setTranslatableLocale('en');
            $this->em->persist($genre);
            $this->em->flush();
            $genre->setName($ru)->setTranslatableLocale('ru');
            $this->em->persist($genre);
            $this->em->flush();
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::down()
     */
    public function down(Schema $schema)
    {
        $rep = $this->em->getRepository('AnimeDbCatalogBundle:Genre');
        foreach ($this->genres as $en => $ru) {
            $this->em->remove($rep->findOneByName($en));
        }
    }
}
