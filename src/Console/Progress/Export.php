<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Console\Progress;

use AnimeDb\Bundle\CatalogBundle\Console\Progress\PresetOutput;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Output\OutputInterface;
use AnimeDb\Bundle\CatalogBundle\Console\Output\Export as ExportOutput;

/**
 * Export ProgressHelper
 *
 * @package AnimeDb\Bundle\CatalogBundle\Console\Progress
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Export extends PresetOutput
{
    /**
     * Construct
     *
     * @param \Symfony\Component\Console\Helper\ProgressHelper $progress
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string $filename
     */
    public function __construct(ProgressHelper $progress, OutputInterface $output, $filename)
    {
        $progress->setFormat(ProgressHelper::FORMAT_QUIET);
        $output = new ExportOutput($output, $filename, false);
        // reset old value
        $output->write('0%');

        parent::__construct($progress, $output);
    }

    /**
     * Destruct
     */
    public function __destruct()
    {
        // say that scanning is completed
        $this->output->write('100%');
    }
}
