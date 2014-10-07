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

use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\HelperSet;

/**
 * Preset output for ProgressHelper
 *
 * @package AnimeDb\Bundle\CatalogBundle\Console\Progress
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class PresetOutput implements HelperInterface
{
    /**
     * Progress helper
     *
     * @var \Symfony\Component\Console\Helper\ProgressHelper
     */
    protected $progress;

    /**
     * Output
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * Construct
     *
     * @param \Symfony\Component\Console\Helper\ProgressHelper $progress
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct(ProgressHelper $progress, OutputInterface $output)
    {
        $this->progress = $progress;
        $this->output = $output;
    }

    /**
     * Sets the helper set associated with this helper.
     *
     * @param \Symfony\Component\Console\Helper\HelperSet $helperSet
     */
    public function setHelperSet(HelperSet $helperSet = null)
    {
        $this->progress->setHelperSet($helperSet);
    }

    /**
     * Gets the helper set associated with this helper.
     *
     * @return \Symfony\Component\Console\Helper\HelperSet
     */
    public function getHelperSet()
    {
        return $this->progress->getHelperSet();
    }

    /**
     * Sets the progress bar width.
     *
     * @param integer $size The progress bar size
     */
    public function setBarWidth($size)
    {
        $this->progress->setBarWidth($size);
    }

    /**
     * Sets the bar character.
     *
     * @param string $char A character
     */
    public function setBarCharacter($char)
    {
        $this->progress->setBarCharacter($char);
    }

    /**
     * Sets the empty bar character.
     *
     * @param string $char A character
     */
    public function setEmptyBarCharacter($char)
    {
        $this->progress->setEmptyBarCharacter($char);
    }

    /**
     * Sets the progress bar character.
     *
     * @param string $char A character
     */
    public function setProgressCharacter($char)
    {
        $this->progress->setProgressCharacter($char);
    }

    /**
     * Sets the progress bar format.
     *
     * @param string $format The format
     */
    public function setFormat($format)
    {
        $this->progress->setFormat($format);
    }

    /**
     * Sets the redraw frequency.
     *
     * @param integer $freq The frequency in steps
     */
    public function setRedrawFrequency($freq)
    {
        $this->progress->setRedrawFrequency($freq);
    }

    /**
     * Starts the progress output.
     *
     * @param integer|null $max Maximum steps
     */
    public function start($max = null)
    {
        $this->progress->start($this->output, $max);
    }

    /**
     * Advances the progress output X steps.
     *
     * @param integer $step Number of steps to advance
     * @param boolean $redraw Whether to redraw or not
     *
     * @throws \LogicException
     */
    public function advance($step = 1, $redraw = false)
    {
        $this->progress->advance($step, $redraw);
    }

    /**
     * Sets the current progress.
     *
     * @param integer $current The current progress
     * @param boolean $redraw Whether to redraw or not
     *
     * @throws \LogicException
     */
    public function setCurrent($current, $redraw = false)
    {
        $this->progress->setCurrent($current, $redraw);
    }

    /**
     * Outputs the current progress string.
     *
     * @param boolean $finish Forces the end result
     *
     * @throws \LogicException
     */
    public function display($finish = false)
    {
        $this->progress->display($finish);
    }

    /**
     * Finishes the progress output.
     */
    public function finish()
    {
        $this->progress->finish();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->progress->getName();
    }
}
