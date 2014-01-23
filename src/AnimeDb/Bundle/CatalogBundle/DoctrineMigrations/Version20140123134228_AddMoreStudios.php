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
class Version20140123134228_AddMoreStudios extends AbstractMigration
{
    /**
     * List studios
     *
     * @var array
     */
    protected $studios = [
        'KSS',
        'Clamp',
        'animate',
        'Agent 21',
        'Radix',
        'Triangle Staff',
        'Kitty Films',
        'CoMix Wave Inc.',
        'Hal Film Maker',
        'Green Bunny',
        'Pink Pineapple'
    ];

    public function up(Schema $schema)
    {
        foreach ($this->studios as $studio) {
            $this->addSql('INSERT INTO "studio" VALUES(NULL, :name)', ['name' => $studio]);
        }
    }

    public function down(Schema $schema)
    {
        foreach ($this->studios as $studio) {
            $this->addSql('DELETE FROM "studio" WHERE name = :name', ['name' => $studio]);
        }
    }
}
