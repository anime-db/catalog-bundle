<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Console\Progress;

use AnimeDb\Bundle\CatalogBundle\Console\Progress\Export;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test export progress
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Console\Progress
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ExportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Root dir
     *
     * @var string
     */
    protected $root;

    protected function setUp()
    {
        $this->root = sys_get_temp_dir().'/test/';
    }

    protected function tearDown()
    {
        $fs = new Filesystem();
        $fs->chmod([$this->root, $this->root.'example.log'], 0777);
        $fs->remove($this->root);
    }

    /**
     * Test export
     */
    public function testExport()
    {
        $filename = $this->root.'example.log';
        /* @var $progress \PHPUnit_Framework_MockObject_MockObject|ProgressHelper */
        $progress = $this->getMock('\Symfony\Component\Console\Helper\ProgressHelper');
        $progress
            ->expects($this->once())
            ->method('setFormat')
            ->with(ProgressHelper::FORMAT_QUIET);
        /* @var $output \PHPUnit_Framework_MockObject_MockObject|OutputInterface */
        $output = $this->getMock('\Symfony\Component\Console\Output\OutputInterface');
        $output
            ->expects($this->at(0))
            ->method('write')
            ->with('0%');
        $output
            ->expects($this->at(1))
            ->method('write')
            ->with('100%');

        $export = new Export($progress, $output, $filename);
        $this->assertEquals('0%', file_get_contents($filename));

        unset($export);
        $this->assertEquals('100%', file_get_contents($filename));
    }
}
