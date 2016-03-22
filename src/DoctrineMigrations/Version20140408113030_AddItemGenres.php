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
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
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
        'Harem' => 'Гарем',
        'Supernatural' => 'Сверхъестественное',
        'Gender Bender' => 'Смена пола'
    ];

    /**
     * Set container
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $rep = $this->em->getRepository('Gedmo\\Translatable\\Entity\\Translation');
        foreach ($this->genres as $en => $ru) {
            $genre = new Genre();

            $genre->setName($en)->setTranslatableLocale('en');
            $this->em->persist($genre);
            $this->em->flush($genre);

            $rep->translate($genre, 'name', 'ru', $ru);
        }
        $this->em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $rep = $this->em->getRepository('AnimeDbCatalogBundle:Genre');
        foreach (array_keys($this->genres) as $en) {
            $this->em->remove($rep->findOneBy(['name' => $en]));
        }
    }
}
