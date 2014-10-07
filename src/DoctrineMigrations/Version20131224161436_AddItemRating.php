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
class Version20131224161436_AddItemRating extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // create temp table from new structure
        $this->addSql('CREATE TABLE "_new" (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            type VARCHAR(16) DEFAULT NULL,
            manufacturer VARCHAR(2) DEFAULT NULL,
            storage INTEGER DEFAULT NULL,
            name VARCHAR(256) NOT NULL,
            date_start DATE NOT NULL,
            date_end DATE DEFAULT NULL,
            duration INTEGER DEFAULT NULL,
            summary TEXT DEFAULT NULL,
            path VARCHAR(256) DEFAULT NULL,
            episodes TEXT DEFAULT NULL,
            episodes_number VARCHAR(5) DEFAULT NULL,
            translate VARCHAR(256) DEFAULT NULL,
            file_info TEXT DEFAULT NULL,
            cover VARCHAR(256) DEFAULT NULL,
            rating INTEGER DEFAULT NULL,
            date_add DATETIME NOT NULL,
            date_update DATETIME NOT NULL
        )');

        $this->addSql('
            INSERT INTO
                "_new"
            SELECT
                id,
                type,
                manufacturer,
                storage,
                name,
                date_start,
                date_end,
                duration,
                summary,
                path,
                episodes,
                episodes_number,
                translate,
                file_info,
                cover,
                0,
                date_add,
                date_update
            FROM
                "item"
        ');
        // rename new to origin and drop origin
        $this->addSql('ALTER TABLE item RENAME TO _origin');
        $this->addSql('ALTER TABLE _new RENAME TO item');
        $this->addSql('DROP TABLE _origin');

        // create index
        $this->addSql('CREATE INDEX item_manufacturer_idx ON item (manufacturer);');
        $this->addSql('CREATE INDEX item_storage_idx ON item (storage);');
        $this->addSql('CREATE INDEX item_type_idx ON item (type)');
        $this->addSql('CREATE INDEX item_rating_idx ON item (rating)');
    }

    public function down(Schema $schema)
    {
        // create temp table from origin structure
        $this->addSql('CREATE TABLE "_new" (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            type VARCHAR(16) DEFAULT NULL,
            manufacturer VARCHAR(2) DEFAULT NULL,
            storage INTEGER DEFAULT NULL,
            name VARCHAR(256) NOT NULL,
            date_start DATE NOT NULL,
            date_end DATE DEFAULT NULL,
            duration INTEGER DEFAULT NULL,
            summary TEXT DEFAULT NULL,
            path VARCHAR(256) DEFAULT NULL,
            episodes TEXT DEFAULT NULL,
            episodes_number VARCHAR(5) DEFAULT NULL,
            translate VARCHAR(256) DEFAULT NULL,
            file_info TEXT DEFAULT NULL,
            cover VARCHAR(256) DEFAULT NULL,
            date_add DATETIME NOT NULL,
            date_update DATETIME NOT NULL
        )');
        $this->addSql('
            INSERT INTO
                "_new"
            SELECT
                id,
                type,
                manufacturer,
                storage,
                name,
                date_start,
                date_end,
                duration,
                summary,
                path,
                episodes,
                episodes_number,
                translate,
                file_info,
                cover,
                date_add,
                date_update
            FROM
                "item"
        ');
        // rename new to origin and drop origin
        $this->addSql('ALTER TABLE item RENAME TO _origin');
        $this->addSql('ALTER TABLE _new RENAME TO item');
        $this->addSql('DROP TABLE _origin');

        // create index
        $this->addSql('CREATE INDEX item_manufacturer_idx ON item (manufacturer);');
        $this->addSql('CREATE INDEX item_storage_idx ON item (storage);');
        $this->addSql('CREATE INDEX item_type_idx ON item (type)');
    }
}
