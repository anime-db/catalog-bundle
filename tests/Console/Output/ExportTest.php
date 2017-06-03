<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Console\Output;

use AnimeDb\Bundle\CatalogBundle\Console\Output\Export;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Test output decorator.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ExportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|OutputInterface
     */
    protected $output;

    /**
     * @var string
     */
    protected $root;

    /**
     * @var string
     */
    protected $file;

    protected function setUp()
    {
        $this->root = sys_get_temp_dir().'/test/';
        $this->file = $this->root.'sub_dir/export.log';
        $this->output = $this->getMock('\Symfony\Component\Console\Output\OutputInterface');
    }

    protected function tearDown()
    {
        $fs = new Filesystem();
        try {
            $fs->chmod([$this->root, $this->file], 0755);
        } catch (IOException $e) {
            // ignore exception
        }
        $fs->remove($this->root);
    }

    /**
     * @expectedException \Symfony\Component\Filesystem\Exception\IOException
     */
    public function testConstructBadDir()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Is always true on Windows.');
        }

        new Export($this->output, '/etc/test/export.log');
    }

    /**
     * @expectedException \Symfony\Component\Filesystem\Exception\IOException
     */
    public function testConstructBadFile()
    {
        mkdir(dirname($this->file), 0755, true);
        touch($this->file);
        chmod($this->file, 0440);

        new Export($this->output, $this->file);
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        $write = [
            [
                'write',
                false,
                'foo',
                'bar',
                true,
                OutputInterface::OUTPUT_NORMAL,
            ],
        ];
        $write = $this->addAppends($write);
        $write = $this->addMessages1($write);
        $write = $this->addMessages2($write);
        $write = $this->addNewlines($write);
        $write = $this->addTypes($write);

        $write_ln = [
            [
                'writeln',
                false,
                'foo',
                'bar',
                true,
                OutputInterface::OUTPUT_NORMAL,
            ],
        ];
        $write_ln = $this->addAppends($write_ln);
        $write_ln = $this->addMessages1($write_ln);
        $write_ln = $this->addMessages2($write_ln);
        $write_ln = $this->addTypes($write_ln);

        return array_merge($write, $write_ln);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    protected function addAppends(array $params)
    {
        $result = [];
        foreach ($params as $param) {
            $param[1] = true;
            $result[] = $param;
        }
        foreach ($params as $param) {
            $param[1] = false;
            $result[] = $param;
        }

        return $result;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    protected function addMessages1(array $params)
    {
        $result = [];
        foreach ($params as $param) {
            $param[2] = 'foo';
            $result[] = $param;
        }
        foreach ($params as $param) {
            $param[2] = ['foo', 'bar'];
            $result[] = $param;
        }

        return $result;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    protected function addMessages2(array $params)
    {
        $result = [];
        foreach ($params as $param) {
            $param[3] = 'baz';
            $result[] = $param;
        }
        foreach ($params as $param) {
            $param[3] = ['baz', 'cor'];
            $result[] = $param;
        }

        return $result;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    protected function addNewlines(array $params)
    {
        $result = [];
        foreach ($params as $param) {
            $param[4] = true;
            $result[] = $param;
        }
        foreach ($params as $param) {
            $param[4] = false;
            $result[] = $param;
        }

        return $result;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    protected function addTypes(array $params)
    {
        $result = [];
        foreach ($params as $param) {
            $param[5] = OutputInterface::OUTPUT_NORMAL;
            $result[] = $param;
        }
        foreach ($params as $param) {
            $param[5] = OutputInterface::OUTPUT_PLAIN;
            $result[] = $param;
        }
        foreach ($params as $param) {
            $param[5] = OutputInterface::OUTPUT_RAW;
            $result[] = $param;
        }

        return $result;
    }

    /**
     * @dataProvider getMessages
     *
     * @param string $method
     * @param bool $append
     * @param string|array $messages1
     * @param string|array $messages2
     * @param bool $newline
     * @param int $type
     */
    public function testWrite($method, $append, $messages1, $messages2, $newline, $type)
    {
        $export = new Export($this->output, $this->file, $append);
        if ($method == 'write') {
            $this->output
                ->expects($this->at(0))
                ->method($method)
                ->with($messages1, $newline, $type);
            $this->output
                ->expects($this->at(1))
                ->method($method)
                ->with($messages2, $newline, $type);
            call_user_func([$export, $method], $messages1, $newline, $type);
            call_user_func([$export, $method], $messages2, $newline, $type);
        } else {
            $newline = true;
            $this->output
                ->expects($this->at(0))
                ->method($method)
                ->with($messages1, $type);
            $this->output
                ->expects($this->at(1))
                ->method($method)
                ->with($messages2, $type);
            call_user_func([$export, $method], $messages1, $type);
            call_user_func([$export, $method], $messages2, $type);
        }
        $expected = '';
        if ($append) {
            foreach ((array) $messages1 as $message) {
                $expected .= strip_tags($message).($newline ? PHP_EOL : '');
            }
        }
        foreach ((array) $messages2 as $message) {
            $expected .= strip_tags($message).($newline ? PHP_EOL : '');
        }
        $export->unlock();

        $this->assertEquals($expected, file_get_contents($this->file));
    }
}
