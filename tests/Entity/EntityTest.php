<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Entity;

use AnimeDb\Bundle\CatalogBundle\Entity\Settings\General;
use AnimeDb\Bundle\CatalogBundle\Entity\Widget\Genre as GenreWidget;
use AnimeDb\Bundle\CatalogBundle\Entity\Widget\Item as ItemWidget;
use AnimeDb\Bundle\CatalogBundle\Entity\Widget\Type as TypeWidget;
use AnimeDb\Bundle\CatalogBundle\Entity\Country;
use AnimeDb\Bundle\CatalogBundle\Entity\Genre;
use AnimeDb\Bundle\CatalogBundle\Entity\Image;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use AnimeDb\Bundle\CatalogBundle\Entity\Label;
use AnimeDb\Bundle\CatalogBundle\Entity\Name;
use AnimeDb\Bundle\CatalogBundle\Entity\Search;
use AnimeDb\Bundle\CatalogBundle\Entity\Source;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use AnimeDb\Bundle\CatalogBundle\Entity\Studio;
use AnimeDb\Bundle\CatalogBundle\Entity\Type;

/**
 * Test entity
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class EntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * List entities
     *
     * @var array
     */
    protected $entities = [];

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->entities['general'] = new General();
        $this->entities['genre_widget'] = new GenreWidget();
        $this->entities['item_widget'] = new ItemWidget();
        $this->entities['type_widget'] = new TypeWidget();
        $this->entities['country'] = new Country();
        $this->entities['genre'] = new Genre();
        $this->entities['image'] = new Image();
        $this->entities['item'] = new Item();
        $this->entities['label'] = new Label();
        $this->entities['name'] = new Name();
        $this->entities['search'] = new Search();
        $this->entities['source'] = new Source();
        $this->entities['storage'] = new Storage();
        $this->entities['studio'] = new Studio();
        $this->entities['type'] = new Type();
    }

    /**
     * Get methods
     *
     * @return array
     */
    public function getMethods()
    {
        return [
            // General
            ['general', 'getSerialNumber', 'setSerialNumber'],
            ['general', 'getTaskScheduler', 'setTaskScheduler', true, false],
            ['general', 'getLocale', 'setLocale'],
            ['general', 'getDefaultSearch', 'setDefaultSearch'],
            // Widget Genre
            ['genre_widget', 'getName', 'setName'],
            ['genre_widget', 'getLink', 'setLink'],
            // Widget Item
            ['item_widget', 'getCover', 'setCover'],
            ['item_widget', 'getName', 'setName'],
            ['item_widget', 'getLink', 'setLink'],
            ['item_widget', 'getLinkForFill', 'setLinkForFill'],
            // Widget Type
            ['type_widget', 'getName', 'setName'],
            ['type_widget', 'getLink', 'setLink'],
            // Country
            ['country', 'getId', 'setId', 0, 123],
            ['country', 'getName', 'setName'],
            // Genre
            ['genre', 'getName', 'setName'],
            // Image
            ['image', 'getSource', 'setSource'],
            ['image', 'getFilename', 'setFilename'],
            // Item
            ['item', 'getName', 'setName'],
            ['item', 'getDuration', 'setDuration', 0, 123],
            ['item', 'getSummary', 'setSummary'],
            ['item', 'getEpisodes', 'setEpisodes'],
            ['item', 'getTranslate', 'setTranslate'],
            ['item', 'getFileInfo', 'setFileInfo'],
            ['item', 'getCover', 'setCover'],
            ['item', 'getFilename', 'setFilename'],
            ['item', 'getEpisodesNumber', 'setEpisodesNumber'],
            ['item', 'getRating', 'setRating', 0, 5],
            // Label
            ['label', 'getName', 'setName'],
            // Name
            ['name', 'getName', 'setName'],
            // Search
            ['search', 'getName', 'setName'],
            // Source
            ['source', 'getUrl', 'setUrl'],
            // Storage
            ['storage', 'getName', 'setName'],
            ['storage', 'getDescription', 'setDescription'],
            ['storage', 'getPath', 'setPath'],
            ['storage', 'getType', 'setType'],
            // Studio
            ['studio', 'getName', 'setName'],
            // Type
            ['type', 'getId', 'setId'],
            ['type', 'getName', 'setName'],
        ];
    }

    /**
     * Test getters and setters
     *
     * @dataProvider getMethods
     * 
     * @param string $entity
     * @param string $getter
     * @param string $setter
     * @param mixed $default
     * @param mixed $new
     */
    public function testGetSet($entity, $getter, $setter, $default = '', $new = 'foo')
    {
        $this->assertEquals($default, $this->call($entity, $getter));
        $this->assertEquals($this->entities[$entity], $this->call($entity, $setter, $new));
        $this->assertEquals($new, $this->call($entity, $getter));
    }

    /**
     * Get methods DateTime
     *
     * @return array
     */
    public function getMethodsTime()
    {
        $now = new \DateTime();
        return [
            // Notice
            ['notice', 'getDateClosed', 'setDateClosed'],
            ['notice', 'getDateStart', 'setDateStart', $now],
            // Plugin
            ['plugin', 'getDateInstall', 'setDateInstall', $now],
            // Task
            ['task', 'getLastRun', 'setLastRun'],
            ['task', 'getNextRun', 'setNextRun', $now]
        ];
    }

    /**
     * Test getters and setters DateTime
     *
     * @dataProvider getMethodsTime
     * 
     * @param string $entity
     * @param string $getter
     * @param string $setter
     * @param mixed $default
     * @param mixed $new
     */
//     public function testGetSetTime($entity, $getter, $setter, $default = null)
//     {
//         $new = (new \DateTime())->modify('+100 seconds');
//         if ($default) {
//             $this->assertInstanceOf('\DateTime', $this->call($entity, $getter));
//         } else {
//             $this->assertNull($this->call($entity, $getter));
//         }
//         $this->assertEquals($this->entities[$entity], $this->call($entity, $setter, $new));
//         $this->assertEquals($new, $this->call($entity, $getter));
//     }

    /**
     * Call entity method
     *
     * @param string $entity
     * @param string $method
     * @param mixed $param
     *
     * @return mixed
     */
    protected function call($entity, $method, $params = null)
    {
        $params = func_get_args();
        $entity = array_shift($params);
        $method = array_shift($params);
        return call_user_func_array([$this->entities[$entity], $method], $params);
    }
}
