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

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140122122107_RenameItemDateStartToDatePremiere extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // create temp table from new structure
        $this->addSql('CREATE TABLE "_new" (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            type VARCHAR(16) DEFAULT NULL,
            country VARCHAR(2) DEFAULT NULL,
            storage INTEGER DEFAULT NULL,
            studio INTEGER DEFAULT NULL,
            name VARCHAR(256) NOT NULL,
            date_premiere DATE DEFAULT NULL,
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
                country,
                storage,
                studio,
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
                rating,
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
        $this->addSql('CREATE INDEX item_country_idx ON item (country);');
        $this->addSql('CREATE INDEX item_storage_idx ON item (storage);');
        $this->addSql('CREATE INDEX item_type_idx ON item (type)');
        $this->addSql('CREATE INDEX item_rating_idx ON item (rating)');
        $this->addSql('CREATE INDEX item_studio_idx ON item (studio)');
    }

    public function down(Schema $schema)
    {
        // create temp table from origin structure
        $this->addSql('CREATE TABLE "_new" (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            type VARCHAR(16) DEFAULT NULL,
            country VARCHAR(2) DEFAULT NULL,
            storage INTEGER DEFAULT NULL,
            studio INTEGER DEFAULT NULL,
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
                country,
                storage,
                studio,
                name,
                CASE WHEN date_premiere IS NOT NULL
                THEN date_premiere
                ELSE "'.date('Y-m-d H:i:s').'"
                END,
                date_end,
                duration,
                summary,
                path,
                episodes,
                episodes_number,
                translate,
                file_info,
                cover,
                rating,
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
        $this->addSql('CREATE INDEX item_country_idx ON item (country);');
        $this->addSql('CREATE INDEX item_storage_idx ON item (storage);');
        $this->addSql('CREATE INDEX item_type_idx ON item (type)');
        $this->addSql('CREATE INDEX item_rating_idx ON item (rating)');
    }
}
