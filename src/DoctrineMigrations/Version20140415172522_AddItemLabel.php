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
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140415172522_AddItemLabel extends AbstractMigration
{
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
}
