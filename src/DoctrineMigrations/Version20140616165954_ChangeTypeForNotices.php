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
class Version20140616165954_ChangeTypeForNotices extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        foreach ($this->getNoticeTypes() as $prefix => $type) {
            $this->addSql('
                UPDATE
                    notice
                SET
                    type = ?
                WHERE
                    message LIKE ?',
                [$type, $prefix.'%']
            );
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        foreach ($this->getNoticeTypes() as $prefix => $type) {
            $this->addSql('
                UPDATE
                    notice
                SET
                    type = ?
                WHERE
                    message LIKE ?',
                ['no_type', $prefix.'%']
            );
        }
    }

    /**
     * Get notice types.
     *
     * @return array
     */
    public function getNoticeTypes()
    {
        return [
            'Detected and added new item ' => 'added_new_item',
            'Files for item ' => 'item_files_not_found',
            'Detected files for new item ' => 'detected_files_for_new_item',
            'Changes are detected in files of item ' => 'updated_item_files',
            'Обнаружена и добавлена новая запись ' => 'added_new_item',
            'Файлы для записи ' => 'item_files_not_found',
            'Обнаружены файлы для новой записи ' => 'detected_files_for_new_item',
            'Обнаружены изменения файлов записи ' => 'updated_item_files',
        ];
    }
}
