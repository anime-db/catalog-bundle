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
class Version20140211134113_AddDateUpdateForStorage extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // create temp table from new structure
        $this->addSql('CREATE TABLE "_new" (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            name VARCHAR(128) NOT NULL,
            description TEXT DEFAULT NULL,
            type VARCHAR(16) NOT NULL,
            path TEXT DEFAULT NULL,
            date_update DATE NOT NULL,
            file_modified DATE DEFAULT NULL
        )');

        $this->addSql('
            INSERT INTO
                "_new"
            SELECT
                id,
                name,
                description,
                type,
                path,
                CASE WHEN modified IS NOT NULL
                THEN modified
                ELSE "'.date('Y-m-d H:i:s').'"
                END,
                modified
            FROM
                "storage"
        ');
        // rename new to origin and drop origin
        $this->addSql('ALTER TABLE storage RENAME TO _origin');
        $this->addSql('ALTER TABLE _new RENAME TO storage');
        $this->addSql('DROP TABLE _origin');

        $this->addSql('CREATE INDEX storage_type_idx ON storage (type)');
    }

    public function down(Schema $schema)
    {
        // create temp table from origin structure
        $this->addSql('CREATE TABLE "_new" (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            name VARCHAR(128) NOT NULL,
            description TEXT DEFAULT NULL,
            type VARCHAR(16) NOT NULL,
            path TEXT DEFAULT NULL,
            modified DATE DEFAULT NULL
        )');

        $this->addSql('
            INSERT INTO
                "_new"
            SELECT
                id, name, description, type, path, file_modified
            FROM
                "storage"
        ');
        // rename new to origin and drop origin
        $this->addSql('ALTER TABLE storage RENAME TO _origin');
        $this->addSql('ALTER TABLE _new RENAME TO storage');
        $this->addSql('DROP TABLE _origin');

        $this->addSql('CREATE INDEX storage_type_idx ON storage (type)');
    }
}
