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
use AnimeDb\Bundle\AppBundle\Entity\Notice;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140423163105_AddReadGuideNotice extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * Entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

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
        $notice = new Notice();
        $notice->setLifetime(259200); // 3 day
        $notice->setMessage($this->getMessage());
        $this->em->persist($notice);
        $this->em->flush();
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::down()
     */
    public function down(Schema $schema)
    {
        $rep = $this->em->getRepository('AnimeDbAppBundle:Notice');
        $notice = $rep->findOneByMessage($this->getMessage());
        if ($notice instanceof Notice) {
            $this->em->remove($notice);
            $this->em->flush();
        }
    }

    /**
     * Get message
     *
     * @return string
     */
    protected function getMessage()
    {
        return 'We are thankful that you\'ve installed our application. We recommend <a href="http://anime-db.org/en/guide/start.html">read the quick guide</a>.';
    }
}