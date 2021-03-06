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
use Doctrine\DBAL\Migrations\SkipMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131015113854_ChangeImagePaths extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * Media dir.
     *
     * @var string
     */
    protected $media_dir;

    /**
     * Set container.
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->media_dir = $container->getParameter('kernel.root_dir').'/../web/media/';
    }

    /**
     * @param Schema $schema
     *
     * @throws SkipMigrationException
     */
    public function up(Schema $schema)
    {
        /*
         * Migration is not critical
         * Old format for storing image files to the new format
         *
         * Old format(date added image):
         *    media/{Y}/{m}/
         *
         * New Format(date added item):
         *    media/{Y}/{m}/{d}/{His}/
         */

        // move covers
        $items = $this->connection->fetchAll('
            SELECT
                `id`,
                `cover`,
                `date_add`
            FROM
                `item`
            WHERE
                `cover` IS NOT NULL AND
                `cover` != "" AND
                `cover`  NOT LIKE "example/%"'
        );
        foreach ($items as $item) {
            $path = date('Y/m/d/His/', strtotime($item['date_add']));
            $file = new File($this->media_dir.$item['cover']);
            $file->move($this->media_dir.$path);
            $this->addSql('
                UPDATE
                    `item`
                SET
                    `cover` = ?
                WHERE
                    `id` = ?',
                [
                    $path.$file->getBasename(),
                    $item['id'],
                ]
            );
        }

        // move images
        $images = $this->connection->fetchAll('
            SELECT
                im.`id`,
                im.`source`,
                i.`date_add`
            FROM
                `item` AS `i`
            INNER JOIN
                `image` AS `im`
                ON
                    im.`item` = i.`id`'
        );
        foreach ($images as $image) {
            $path = date('Y/m/d/His/', strtotime($image['date_add']));
            $file = new File($this->media_dir.$image['source']);
            $file->move($this->media_dir.$path);
            $this->addSql('
                UPDATE
                    `image`
                SET
                    `source` = ?
                WHERE
                    `id` = ?',
                [
                    $path.$file->getBasename(),
                    $image['id'],
                ]
            );
        }

        // skip if no data
        $this->skipIf(!($items && $images), 'No data to migrate');
    }

    /**
     * @param Schema $schema
     *
     * @throws SkipMigrationException
     */
    public function down(Schema $schema)
    {
        // the down migration is not need
        $this->skipIf(true, 'The down migration is not need');
    }
}
