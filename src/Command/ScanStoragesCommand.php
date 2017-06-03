<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Command;

use AnimeDb\Bundle\CatalogBundle\Console\Output\Decorator;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use Symfony\Component\Finder\Finder;
use AnimeDb\Bundle\CatalogBundle\Event\Listener\Entity\Storage as StorageListener;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\StoreEvents;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\UpdateItemFiles;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\DetectedNewFiles;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\DeleteItemFiles;
use AnimeDb\Bundle\CatalogBundle\Repository\Storage as StorageRepository;
use Symfony\Component\Finder\SplFileInfo;
use AnimeDb\Bundle\CatalogBundle\Console\Output\LazyWrite;
use AnimeDb\Bundle\CatalogBundle\Console\Progress\Export;
use AnimeDb\Bundle\CatalogBundle\Console\Progress\PresetOutput;
use Symfony\Component\Console\Output\NullOutput;
use Patchwork\Utf8;

/**
 * Scan storages for new items.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ScanStoragesCommand extends ContainerAwareCommand
{
    /**
     * Allowable extension.
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
        'ismv',
    ];

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
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Ignore the last modified storage'
            )
            ->addOption(
                'no-progress',
                null,
                InputOption::VALUE_NONE,
                'Disable progress bar'
            )
            ->addOption(
                'export',
                null,
                InputOption::VALUE_REQUIRED,
                'Export progress to file (disables progress as --no-progress)'
            )
            ->setHelp(<<<'EOT'
Example scan all storages:

<info>php app/console animedb:scan-storage</info>

Example scan storage with id <info>1</info>:

<info>php app/console animedb:scan-storage 1</info>
EOT
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = time();

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $dispatcher = $this->getContainer()->get('event_dispatcher');
        /* @var $rep StorageRepository */
        $rep = $em->getRepository('AnimeDbCatalogBundle:Storage');

        $progress = $this->getProgress($input, $output);
        $lazywrite = new LazyWrite($output);
        $lazywrite->setLazyWrite(!$input->getOption('no-progress'));

        // get list storages
        if ($id = $input->getArgument('storage')) {
            $storage = $rep->find($id);
            if (!($storage instanceof Storage)) {
                throw new \InvalidArgumentException('Not found the storage with id: '.$id);
            }
            if (!$storage->isWritable()) {
                throw new \InvalidArgumentException('Storage "'.$storage->getName().'" can not be scanned');
            }
            $storages = [$storage];
        } else {
            $storages = $rep->getList(Storage::getTypesWritable());
        }

        /* @var $storage Storage */
        foreach ($storages as $storage) {
            $output->writeln('Scan storage <info>'.$storage->getName().'</info>:');

            $path = $storage->getPath();
            $path = Utf8::wrapPath($path); // wrap path for current fs

            if (!file_exists($path)) {
                $output->writeln('Storage is not available');
                continue;
            }

            // check storage id
            $owner = $this->checkStorageId($path, $storage, $rep);
            if ($owner instanceof Storage) {
                $output->writeln('Path <info>'.$storage->getPath().'</info> reserved storage <info>'
                    .$owner->getName().'</info>');
                continue;
            }

            // storage not modified
            if (!$input->getOption('force') &&
                $storage->getFileModified() &&
                filemtime($path) == $storage->getFileModified()->getTimestamp()
            ) {
                $output->writeln('Storage is not modified');
                continue;
            }

            $files = $this->getFilesByPath($path);
            $total = $files->count();
            // total files +1% for check of delete files
            $progress->start(ceil($total + ($total * 0.01)));
            $progress->display();

            /* @var $file SplFileInfo */
            foreach ($files as $file) {
                // ignore not supported files
                if ($file->isFile() && !$this->isAllowFile($file)) {
                    $progress->advance();
                    continue;
                }

                // item is exists and modified
                if ($item = $this->getItemFromFile($storage, $file)) {
                    if ($item->getDateUpdate()->getTimestamp() < $file->getPathInfo()->getMTime()) {
                        $dispatcher->dispatch(StoreEvents::UPDATE_ITEM_FILES, new UpdateItemFiles($item));
                        $lazywrite->writeln('Changes are detected in files of item <info>'.$item->getName().'</info>');
                    }
                } else {
                    // remove wrap prefix
                    list(, $file) = explode('://', $file->getPathname(), 2);
                    $file = new SplFileInfo($file, '', '');

                    // it is a new item
                    $name = $this->getContainer()->get('anime_db.storage.filename_cleaner')->clean($file);
                    $dispatcher->dispatch(
                        StoreEvents::DETECTED_NEW_FILES,
                        new DetectedNewFiles($storage, $file, $name)
                    );
                    $lazywrite->writeln('Detected files for new item <info>'.$file->getFilename().'</info>');
                }
                $progress->advance();
            }
            $em->refresh($storage);

            // check of delete file for item
            foreach ($this->getItemsOfDeletedFiles($storage, $files) as $item) {
                $dispatcher->dispatch(StoreEvents::DELETE_ITEM_FILES, new DeleteItemFiles($item));
                $lazywrite->writeln('<error>Files for item "'.$item->getName().'" is not found</error>');
            }
            $progress->advance();
            $progress->finish();
            $lazywrite->writeAll();

            // update date modified
            $storage->setFileModified(new \DateTime(date('Y-m-d H:i:s', filemtime($path))));
            $em->persist($storage);
            $output->writeln('');
        }
        $em->flush();

        $output->writeln('Time: <info>'.(time() - $start).'</info> s.');
    }

    /**
     * Get items of deleted files.
     *
     * @param Storage $storage
     * @param Finder $finder
     *
     * @return array
     */
    protected function getItemsOfDeletedFiles(Storage $storage, Finder $finder)
    {
        $items = [];
        // check of delete file for item
        foreach ($storage->getItems() as $item) {
            foreach ($finder as $file) {
                if (pathinfo($item->getPath(), PATHINFO_BASENAME) == $file->getFilename()) {
                    continue 2;
                }
            }
            $items[] = $item;
        }

        return $items;
    }

    /**
     * Get item from files.
     *
     * @param Storage $storage
     * @param SplFileInfo $file
     *
     * @return Item|bool
     */
    protected function getItemFromFile(Storage $storage, SplFileInfo $file)
    {
        /* @var $item Item */
        foreach ($storage->getItems() as $item) {
            if (pathinfo($item->getPath(), PATHINFO_BASENAME) == $file->getFilename()) {
                return $item;
            }
        }

        return false;
    }

    /**
     * Get files by path.
     *
     * @param string $path
     *
     * @return Finder
     */
    protected function getFilesByPath($path)
    {
        return Finder::create()
            ->in($path)
            ->ignoreUnreadableDirs()
            ->depth('== 0')
            ->notName('.*');
    }

    /**
     * Is allow file.
     *
     * @param SplFileInfo $file
     *
     * @return bool
     */
    protected function isAllowFile(SplFileInfo $file)
    {
        return in_array(strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION)), $this->allow_ext);
    }

    /**
     * Update storage id.
     *
     * @param string $path
     * @param Storage $storage
     * @param StorageRepository $rep
     *
     * @return Storage|bool
     */
    protected function checkStorageId($path, Storage $storage, StorageRepository $rep)
    {
        if (!file_exists($path.StorageListener::ID_FILE)) {
            // path is free. reserve for me
            file_put_contents($path.StorageListener::ID_FILE, $storage->getId());
        } elseif (file_get_contents($path.StorageListener::ID_FILE) == $storage->getId()) {
            // it is my path. do nothing
        } elseif (!($duplicate = $rep->find(file_get_contents($path.StorageListener::ID_FILE)))) {
            // this path is reserved storage that was removed and now this path is free
            file_put_contents($path.StorageListener::ID_FILE, $storage->getId());
        } else {
            return $duplicate;
        }

        return true;
    }

    /**
     * Get progress.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return Decorator
     */
    protected function getProgress(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('no-progress')) {
            $output = new NullOutput();
        }

        // export progress only for one storage
        if (!$input->getArgument('storage')) {
            $input->setOption('export', null);
        }

        if ($export_file = $input->getOption('export')) {
            // progress is redirected to the export file
            $input->setOption('no-progress', true);
            $progress = new Export($this->getHelperSet()->get('progress'), new NullOutput(), $export_file);
        } else {
            $progress = new PresetOutput($this->getHelperSet()->get('progress'), $output);
        }

        $progress->setBarCharacter('<comment>=</comment>');

        return $progress;
    }
}
