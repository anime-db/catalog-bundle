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
class Version20140410114443_AddIndexOnSourceUrl extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE INDEX source_url_idx ON source (url)');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP INDEX source_url_idx');
    }
}