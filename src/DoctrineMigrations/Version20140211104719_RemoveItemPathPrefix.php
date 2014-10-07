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
class Version20140211104719_RemoveItemPathPrefix extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $items = $this->connection->fetchAll('
            SELECT
                i.`id`,
                i.`path` AS `item_path`,
                s.`path` AS `storage_path`
            FROM
                `item` AS `i`
            INNER JOIN
                `storage` AS `s`
                ON
                    i.`storage` = s.`id`'
        );

        // nothing to migrate
        if (!$items) {
            $this->skipIf(!($items && $images), 'No data to migrate');
        } else {
            foreach ($items as $item) {
                if (strpos($item['item_path'], $item['storage_path']) === 0) {
                    $this->addSql('
                        UPDATE
                            `item`
                        SET
                            `path` = ?
                        WHERE
                            `id` = ?',
                        [
                            substr($item['item_path'], strlen($item['storage_path'])),
                            $item['id']
                        ]
                    );
                }
            }
        }
    }

    public function down(Schema $schema)
    {
        $items = $this->connection->fetchAll('
            SELECT
                i.`id`,
                i.`path` AS `item_path`,
                s.`path` AS `storage_path`
            FROM
                `item` AS `i`
            INNER JOIN
                `storage` AS `s`
                ON
                    i.`storage` = s.`id`'
        );

        // nothing to migrate
        if (!$items) {
            $this->skipIf(!($items && $images), 'No data to migrate');
        } else {
            foreach ($items as $item) {
                $this->addSql('
                    UPDATE
                        `item`
                    SET
                        `path` = ?
                    WHERE
                        `id` = ?',
                    [
                        $item['storage_path'].$item['item_path'],
                        $item['id']
                    ]
                );
            }
        }
    }
}
