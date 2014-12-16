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
class Version20140407162508_RenameItemGenres extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * Entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Rename genres
     *
     * @var array
     */
    protected $rename = [
        'Mysticism' => 'Mystery',
        'Ettie' => 'Ecchi',
        'For children' => ['Kids', 'Детское'],
        'Meho' => 'Mecha',
        'Musical' => ['Music', 'Музыка'],
        'Samurai action' => ['Samurai', 'Самураи'],
        'Senen' => 'Shounen',
        'Senen-ai' => 'Shounen-ai',
        'Psychology' => ['Psychological', 'Психологическое'],
        'Fantastic' => 'Sci-fi',
        'Everyday' => 'Slice of life',
        'Vampires' => 'Vampire',
        'Urey' => 'Yuri'
    ];

    /**
     * Restore genres
     *
     * @var array
     */
    protected $restore = [
        'Mystery' => 'Mysticism',
        'Ecchi' => 'Ettie',
        'Kids' => ['For children', 'Для детей'],
        'Mecha' => 'Meho',
        'Music' => ['Musical', 'Музыкальный'],
        'Samurai' => ['Samurai action', 'Самурайский боевик'],
        'Shounen' => 'Senen',
        'Shounen-ai' => 'Senen-ai',
        'Psychological' => ['Psychology', 'Психология'],
        'Sci-fi' => 'Fantastic',
        'Slice of life' => 'Everyday',
        'Vampire' => 'Vampires',
        'Yuri' => 'Urey'
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
        $rep = $this->em->getRepository('AnimeDbCatalogBundle:Genre');

        /* @var $genre \AnimeDb\Bundle\CatalogBundle\Entity\Genre */
        foreach ($this->rename as $from => $to) {
            $genre = $rep->findOneByName($from);
            if (is_array($to)) {
                $genre->setName($to[1])->setTranslatableLocale('ru');
                $this->em->persist($genre);
                $this->em->flush($genre);
                $to = $to[0];
            }
            $genre->setName($to)->setTranslatableLocale('en');
            $this->em->persist($genre);
        }
        // remove
        $genre = $rep->findOneByName('Mystery play');
        $this->em->remove($genre);

        // rename russian
        $genre = $rep->findOneByName('History');
        $genre->setName('Исторический')->setTranslatableLocale('ru');
        $this->em->persist($genre);

        $genre = $rep->findOneByName('War');
        $genre->setName('Военное')->setTranslatableLocale('ru');
        $this->em->persist($genre);

        $this->em->flush();
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::down()
     */
    public function down(Schema $schema)
    {
        $rep = $this->em->getRepository('AnimeDbCatalogBundle:Genre');

        /* @var $genre \AnimeDb\Bundle\CatalogBundle\Entity\Genre */
        foreach ($this->restore as $from => $to) {
            $genre = $rep->findOneByName($from);
            if (is_array($to)) {
                $genre->setName($to[1])->setTranslatableLocale('ru');
                $this->em->persist($genre);
                $this->em->flush($genre);
                $to = $to[0];
            }
            $genre->setName($to)->setTranslatableLocale('en');
            $this->em->persist($genre);
        }
        // new genre
        $genre = new Genre();
        $genre->setName('Mystery play')->setTranslatableLocale('en');
        $this->em->persist($genre);
        $this->em->flush();
        $genre->setName('Мистерия')->setTranslatableLocale('ru');
        $this->em->persist($genre);

        // rename russian
        $genre = $rep->findOneByName('History');
        $genre->setName('История')->setTranslatableLocale('ru');
        $this->em->persist($genre);

        $genre = $rep->findOneByName('War');
        $genre->setName('Война')->setTranslatableLocale('ru');
        $this->em->persist($genre);

        $this->em->flush();
    }
}
