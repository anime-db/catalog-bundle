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
use AnimeDb\Bundle\CatalogBundle\Entity\Label;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140415172522_AddItemLabel extends AbstractMigration implements ContainerAwareInterface
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
        $this->addSql('CREATE TABLE label (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            name VARCHAR(16) NOT NULL
        )');
        // add index
        $this->addSql('CREATE INDEX label_name_idx ON label (name)');

        $this->addSql('CREATE TABLE items_labels (
            item_id INTEGER NOT NULL,
            label_id INTEGER NOT NULL,
            PRIMARY KEY(item_id, label_id)
        )');
        // add index
        $this->addSql('CREATE INDEX item_labels_item_id_idx ON items_labels (item_id)');
        $this->addSql('CREATE INDEX item_labels_label_id_idx ON items_labels (label_id)');
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::down()
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('label');
        $schema->dropTable('items_labels');
    }

    /**
     * (non-PHPdoc)
     * @see Doctrine\DBAL\Migrations.AbstractMigration::postUp()
     */
    public function postUp(Schema $schema)
    {
        $this->em->persist((new Label())->setName('Scheduled'));
        $this->em->persist((new Label())->setName('Watching'));
        $this->em->persist((new Label())->setName('Views'));
        $this->em->persist((new Label())->setName('Postponed'));
        $this->em->persist((new Label())->setName('Dropped'));
        // russian
        $this->em->persist((new Label())->setName('Запланировано'));
        $this->em->persist((new Label())->setName('Смотрю'));
        $this->em->persist((new Label())->setName('Просмотрено'));
        $this->em->persist((new Label())->setName('Отложено'));
        $this->em->persist((new Label())->setName('Брошено'));
        $this->em->flush();
    }
}