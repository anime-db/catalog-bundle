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
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\HelperSet;

/**
 * Preset output for ProgressHelper.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class PresetOutput implements HelperInterface
{
    /**
     * @var ProgressHelper
     */
    protected $progress;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @param ProgressHelper $progress
     * @param OutputInterface $output
     */
    public function __construct(ProgressHelper $progress, OutputInterface $output)
    {
        $this->progress = $progress;
        $this->output = $output;
    }

    /**
     * Sets the helper set associated with this helper.
     *
     * @param HelperSet $helperSet
     */
    public function setHelperSet(HelperSet $helperSet = null)
    {
        $this->progress->setHelperSet($helperSet);
    }

    /**
     * Gets the helper set associated with this helper.
     *
     * @return HelperSet
     */
    public function getHelperSet()
    {
        return $this->progress->getHelperSet();
    }

    /**
     * Sets the progress bar width.
     *
     * @param int $size The progress bar size
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
     * @param int $freq The frequency in steps
     */
    public function setRedrawFrequency($freq)
    {
        $this->progress->setRedrawFrequency($freq);
    }

    /**
     * Starts the progress output.
     *
     * @param int|null $max Maximum steps
     */
    public function start($max = null)
    {
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
        $this->progress->advance($step, $redraw);
    }

    /**
     * Sets the current progress.
     *
     * @param int $current The current progress
     * @param bool $redraw Whether to redraw or not
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
     * @param bool $finish Forces the end result
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
     * @return string
     */
    public function getName()
    {
        return $this->progress->getName();
    }
}
