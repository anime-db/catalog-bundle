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
use Symfony\Component\Console\Input\InputArgument;
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
     * Allowable extension
     *
     * @var array
     */
    protected $allow_ext = [
        'avi',
        'mkv',
        'm1v',
        'm2v',
        'm4v',
        'mov',
        'qt',
        'mpeg',
        'mpg',
        'mpe',
        'ogg',
        'rm',
        'wmv',
        'asf',
        'wm',
        'm2ts',
        'mts',
        'm2t',
        'mp4',
        'mov',
        '3gp',
        '3g2',
        'k3g',
        'mp2',
        'mpv2',
        'mod',
        'vob',
        'f4v',
        'ismv'
    ];

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('animedb:scan-storage')
            ->setDescription('Scan storages for new items')
            ->addArgument(
                'storage',
                InputArgument::OPTIONAL,
                'Id scanned storage'
            )
            ->setHelp(<<<EOT
Example scan all storages:

<info>php app/console animedb:scan-storage</info>

Example scan storage with id <info>1</info>:

<info>php app/console animedb:scan-storage 1</info>
EOT
            );
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $dispatcher = $this->getContainer()->get('event_dispatcher');
        /* @var $repository \AnimeDb\Bundle\CatalogBundle\Repository\Storage */
        $repository = $em->getRepository('AnimeDbCatalogBundle:Storage');

        $start = time();

        if ($input->getArgument('storage')) {
            $storage = $repository->find($input->getArgument('storage'));
            if (!($storage instanceof Storage)) {
                throw new \InvalidArgumentException('Not found the storage with id: '.$input->getArgument('storage'));
            }
            $storages = [$storage];
        } else {
            $storages = $repository->getList(Storage::getTypesWritable());
        }

        /* @var $storage \AnimeDb\Bundle\CatalogBundle\Entity\Storage */
        foreach ($storages as $storage) {
            $output->writeln('');
            $output->writeln('Scan storage <info>'.$storage->getName().'</info>:');

            $path = $storage->getPath();
            // wrap fs
            if (defined('PHP_WINDOWS_VERSION_BUILD')) {
                stream_wrapper_register('win', 'Patchwork\Utf8\WinFsStreamWrapper');
                $path = 'win://'.$path;
            }

            if (!file_exists($path)) {
                $output->writeln('Storage is not available');
                continue;
            }

            // update storage id if can
            if (!file_exists($path.Storage::ID_FILE)) {
                file_put_contents($path.Storage::ID_FILE, $storage->getId());
            } elseif (file_get_contents($path.Storage::ID_FILE) == $storage->getId()) {
                // it my path. do nothing
            } elseif (!($duplicate = $repository->find(file_get_contents($path.Storage::ID_FILE)))) {
                // this path is reserved storage that was removed and now this path is free
                file_put_contents($path.Storage::ID_FILE, $storage->getId());
            } else {
                /* @var $duplicate \AnimeDb\Bundle\CatalogBundle\Entity\Storage */
                $output->writeln('Path <info>'.$storage->getPath().'</info> reserved storage <info>'
                    .$duplicate->getName().'</info>');
                continue;
            }

            // storage not modified
            if ($storage->getFileModified() && filemtime($path) == $storage->getFileModified()->getTimestamp()) {
                $output->writeln('Storage is not modified');
                continue;
            }

            $finder = new Finder();
            $finder
                ->in($path)
                ->ignoreUnreadableDirs()
                ->depth('== 0')
                ->notName('.*');

            /* @var $file \Symfony\Component\Finder\SplFileInfo */
            foreach ($finder as $file) {
                // ignore not supported files
                if ($file->isFile() &&
                    !in_array(strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION)), $this->allow_ext)
                ) {
                    continue;
                }

                // item is exists and modified
                if ($item = $this->getItemFromFile($storage, $file)) {
                    if ($item->getDateUpdate()->getTimestamp() < $file->getPathInfo()->getMTime()) {
                        $dispatcher->dispatch(StoreEvents::UPDATE_ITEM_FILES, new UpdateItemFiles($item));
                        $output->writeln('Changes are detected in files of item <info>'.$item->getName().'</info>');
                    }
                } else {
                    // remove win:// if need
                    if (defined('PHP_WINDOWS_VERSION_BUILD')) {
                        $file = new SplFileInfo(substr($file->getPathname(), 6), '', '');
                    }

                    // it is a new item
                    $name = $file->isDir() ? $file->getFilename() : pathinfo($file->getFilename(), PATHINFO_BASENAME);
                    $dispatcher->dispatch(StoreEvents::DETECTED_NEW_FILES, new DetectedNewFiles($storage, $file));
                    $output->writeln('Detected files for new item <info>'.$name.'</info>');
                }
            }
            $em->refresh($storage);

            // check of delete file for item
            foreach ($this->getItemsOfDeletedFiles($storage, $finder) as $item) {
                $dispatcher->dispatch(StoreEvents::DELETE_ITEM_FILES, new DeleteItemFiles($item));
                $output->writeln('<error>Files for item "'.$item->getName().'" is not found</error>');
            }

            // update date modified
            $storage->setFileModified(new \DateTime(date('Y-m-d H:i:s', filemtime($path))));
            $em->persist($storage);
        }
        $em->flush();

        $output->writeln('');
        $output->writeln('Time: <info>'.(time()-$start).'</info> s.');
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
     * Get item from files
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     * @param \Symfony\Component\Finder\SplFileInfo $file
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item|boolean
     */
    protected function getItemFromFile(Storage $storage, SplFileInfo $file)
    {
        // remove win:// if need
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $path = substr($file->getPathname(), 6);
        } else {
            $path = $file->getPathname();
        }
        $path .= $file->isDir() ? DIRECTORY_SEPARATOR : '';

        /* @var $item \AnimeDb\Bundle\CatalogBundle\Entity\Item */
        foreach ($storage->getItems() as $item) {
            if ($item->getPath() == $path) {
                return $item;
            }
        }
        return false;
    }
}