<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use Symfony\Component\Finder\Finder;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\StoreEvents;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\UpdateItemFiles;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\DetectedNewFiles;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\DeleteItemFiles;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Scan storages for new items
 *
 * @package AnimeDb\Bundle\CatalogBundle\Command
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ScanStoragesCommand extends ContainerAwareCommand
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::configure()
     */
    protected function configure()
    {
        $this->setName('animedb:scan-storage')
            ->setDescription('Scan storages for new items');
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        $start = microtime(true);

        $storages = $em->getRepository('AnimeDbCatalogBundle:Storage')
            ->getList(Storage::getTypesWritable());

        /* @var $storage \AnimeDb\Bundle\CatalogBundle\Entity\Storage */
        foreach ($storages as $storage) {
            $output->writeln('');
            $name = $storage->getName();
            if (defined('PHP_WINDOWS_VERSION_BUILD')) {
                $name = iconv('utf-8','cp866', $name);
            }
            $output->writeln('Scan storage <info>'.$name.'</info>:');

            $path = $storage->getPath();
            // wrap fs
            if (defined('PHP_WINDOWS_VERSION_BUILD')) {
                stream_wrapper_register('win', 'Patchwork\Utf8\WinFsStreamWrapper');
                $path = 'win://'.$path;
            }

            // storage is exists and not modified
            if (!file_exists($path) || (
                $storage->getFileModified() &&
                filemtime($path) == $storage->getFileModified()->getTimestamp()
            )) {
                continue;
            }

            $finder = new Finder();
            $finder
                ->in($path)
                ->ignoreUnreadableDirs()
                ->depth('== 0')
                // tmp files
                ->notName('.*')
                ->notName('*~')
                ->notName('ehthumbs.db')
                ->notName('Thumbs.db')
                ->notName('desktop.ini');

            /* @var $file \Symfony\Component\Finder\SplFileInfo */
            foreach ($finder as $file) {
                // remove win:// if need
                if (defined('PHP_WINDOWS_VERSION_BUILD')) {
                    $file = new SplFileInfo(substr($file->getPathname(), 6), '', '');
                }

                if ($item = $this->getItemOfUpdatedFiles($storage, $file)) {
                    $name = $item->getName();
                    if (defined('PHP_WINDOWS_VERSION_BUILD')) {
                        $name = iconv('utf-8','cp866', $name);
                    }
                    $dispatcher->dispatch(StoreEvents::UPDATE_ITEM_FILES, new UpdateItemFiles($item));
                    $output->writeln('Changes are detected in files of item <info>'.$name.'</info>');
                } else {
                    // it is a new item
                    $name = $file->isDir() ? $file->getFilename() : pathinfo($file->getFilename(), PATHINFO_BASENAME);
                    if (defined('PHP_WINDOWS_VERSION_BUILD')) {
                        $name = iconv('utf-8','cp866', $name);
                    }
                    $dispatcher->dispatch(StoreEvents::DETECTED_NEW_FILES, new DetectedNewFiles($storage, $file));
                    $output->writeln('Detected files for new item <info>'.$name.'</info>');
                }
            }

            // check of delete file for item
            foreach ($this->getItemsOfDeletedFiles($storage, $finder) as $item) {
                $name = $item->getName();
                if (defined('PHP_WINDOWS_VERSION_BUILD')) {
                    $name = iconv('utf-8','cp866', $name);
                }
                $dispatcher->dispatch(StoreEvents::DELETE_ITEM_FILES, new DeleteItemFiles($item));
                $output->writeln('<error>Files for item "'.$name.'" is removed</error>');
            }

            // update date modified
            $storage->setFileModified(new \DateTime(date('Y-m-d H:i:s', filemtime($path))));
            $em->persist($storage);
        }
        $em->flush();

        $output->writeln('');
        $output->writeln('Time: <info>'.round((microtime(true)-$start)*1000, 2).'</info> s.');
    }

    /**
     * Get items of deleted files
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     * @param \Symfony\Component\Finder\Finder $finder
     *
     * @return array
     */
    protected function getItemsOfDeletedFiles(Storage $storage, Finder $finder)
    {
        $items = [];
        // check of delete file for item
        foreach ($storage->getItems() as $item) {
            foreach ($finder as $file) {
                if ($item->getPath() == $file->getPathname()) {
                    continue 2;
                }
            }
            $items[] = $item;
        }
        return $items;
    }

    /**
     * Get item of updated files
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     * @param \Symfony\Component\Finder\SplFileInfo $file
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item|boolean
     */
    protected function getItemOfUpdatedFiles(Storage $storage, SplFileInfo $file)
    {
        /* @var $item \AnimeDb\Bundle\CatalogBundle\Entity\Item */
        foreach ($storage->getItems() as $item) {
            if ($item->getPath() == $file->getPathname()) {
                // item is exists and modified
                if ($item->getDateUpdate()->getTimestamp() < $file->getPathInfo()->getMTime()) {
                    return $item;
                }
                return false;
            }
        }
        return false;
    }
}