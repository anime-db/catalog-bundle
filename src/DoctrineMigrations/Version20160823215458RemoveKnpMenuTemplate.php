<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160823215458RemoveKnpMenuTemplate extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $tpl;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->fs = $container->get('filesystem');
        $this->tpl = $container->getParameter('kernel.root_dir').'/Resources/knp_menu.html.twig';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->fs->remove($this->tpl);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->fs->copy(__DIR__.'/data/knp_menu.html.twig', $this->tpl);
    }
}
