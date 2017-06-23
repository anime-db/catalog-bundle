<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Console\Progress;

use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Output\OutputInterface;
use AnimeDb\Bundle\CatalogBundle\Console\Output\Export as ExportOutput;

/**
 * Export ProgressHelper.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Export extends PresetOutput
{
    /**
     * @var ExportOutput
     */
    protected $output;

    /**
     * @var int
     */
    private $max = 0;

    /**
     * @var int
     */
    private $current = 0;

    /**
     * @param ProgressHelper $progress
     * @param OutputInterface $output
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
     * Starts the progress output.
     *
     * @param int|null $max Maximum steps
     */
    public function start($max = null)
    {
        $this->max = (int) $max;
        $this->progress->start($this->output, $max);
    }

    /**
     * Advances the progress output X steps.
     *
     * @param int $step Number of steps to advance
     * @param bool $redraw Whether to redraw or not
     *
     * @throws \LogicException
     */
    public function advance($step = 1, $redraw = false)
    {
        parent::advance($step, $redraw);

        $this->current += $step;

        $percent = 0;
        if ($this->max > 0) {
            $percent = (float) $this->current / $this->max;
        }
        $this->output->write(sprintf('%d%%', floor($percent * 100)));
    }

    public function __destruct()
    {
        // say that scanning is completed
        $this->output->write('100%');
        $this->output->unlock();
    }
}
