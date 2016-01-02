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
     * Get entity
     *
     * @param string $entity
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function get($entity)
    {
        return $this->entities[$entity];
    }

    /**
     * Get entity mock
     *
     * @param string $entity
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEntityMock($entity)
    {
        return $this->getMockBuilder('\AnimeDb\Bundle\CatalogBundle\Entity\\'.$entity)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Call entity method
     *
     * @param string $entity
     * @param string $method
     * @param mixed $param
     *
     * @return mixed
     */
    protected function call($entity, $method, $param = null)
    {
        $params = func_get_args();
        $entity = array_shift($params);
        $method = array_shift($params);
        return call_user_func_array([$this->get($entity), $method], $params);
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
            ['item_widget', 'getItem', 'setItem', null, $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item')],
            ['item_widget', 'getItem', 'setItem', null, null],
            // Widget Type
            ['type_widget', 'getName', 'setName'],
            ['type_widget', 'getLink', 'setLink'],
            // Country
            ['country', 'getId', 'setId', 0, 123],
            ['country', 'getName', 'setName'],
            ['country', 'getTranslatableLocale', 'setTranslatableLocale'],
            // Genre
            ['genre', 'getName', 'setName'],
            ['genre', 'getTranslatableLocale', 'setTranslatableLocale'],
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
            ['type', 'getTranslatableLocale', 'setTranslatableLocale'],
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
        $this->assertEquals($this->get($entity), $this->call($entity, $setter, $new));
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
            // Item
            ['item', 'getDateAdd', 'setDateAdd', $now],
            ['item', 'getDateEnd', 'setDateEnd'],
            ['item', 'getDatePremiere', 'setDatePremiere'],
            ['item', 'getDateUpdate', 'setDateUpdate', $now],
            // Search
            ['search', 'getDateAdd', 'setDateAdd'],
            ['search', 'getDateEnd', 'setDateEnd'],
            ['search', 'getDatePremiere', 'setDatePremiere'],
            // Storage
            ['storage', 'getDateUpdate', 'setDateUpdate', $now],
            ['storage', 'getFileModified', 'setFileModified'],
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
    public function testGetSetTime($entity, $getter, $setter, $default = null)
    {
        $new = (new \DateTime())->modify('+100 seconds');
        if ($default) {
            $this->assertInstanceOf('\DateTime', $this->call($entity, $getter));
        } else {
            $this->assertNull($this->call($entity, $getter));
        }
        $this->assertEquals($this->get($entity), $this->call($entity, $setter, $new));
        $this->assertEquals($new, $this->call($entity, $getter));
    }

    /**
     * Get methods to string
     *
     * @return array
     */
    public function getMethodsToString()
    {
        return [
            ['country', 'setName'],
            ['genre', 'setName'],
            ['image', 'setSource'],
            ['item', 'setName'],
            ['label', 'setName'],
            ['name', 'setName'],
            ['source', 'setUrl'],
            ['storage', 'setName'],
            ['studio', 'setName'],
            ['type', 'setName'],
        ];
    }

    /**
     * Test to string
     *
     * @dataProvider getMethodsToString
     *
     * @param string $entity
     * @param string $setter
     */
    public function testToString($entity, $setter)
    {
        $this->call($entity, $setter, 'foo');
        $this->assertEquals('foo', $this->call($entity, '__toString'));
    }

    /**
     * Get methods generated id
     *
     * @return array
     */
    public function getMethodsGeneratedId()
    {
        return [
            ['genre'],
            ['image'],
            ['item'],
            ['label'],
            ['name'],
            ['source'],
            ['storage'],
            ['studio'],
        ];
    }

    /**
     * Test generated id
     *
     * @dataProvider getMethodsGeneratedId
     *
     * @param string $entity
     */
    public function testGeneratedId($entity)
    {
        $this->assertEmpty($this->call($entity, 'getId'));
    }

    /**
     * Get methods one to many
     *
     * @return array
     */
    public function getMethodsOneToMany()
    {
        return [
            ['country', 'Item', 'getItems', 'addItem', 'removeItem', 'setCountry'],
            ['country', 'CountryTranslation', 'getTranslations', 'addTranslation', 'removeTranslation', 'setObject'],
            ['storage', 'Item', 'getItems', 'addItem', 'removeItem', 'setStorage'],
            ['studio', 'Item', 'getItems', 'addItem', 'removeItem', 'setStudio'],
            ['type', 'Item', 'getItems', 'addItem', 'removeItem', 'setType']
        ];
    }

    /**
     * Test one to many
     *
     * @dataProvider getMethodsOneToMany
     *
     * @param string $entity
     * @param string $related
     * @param string $get
     * @param string $add
     * @param string $remove
     * @param string $set
     */
    public function testOneToMany($entity, $related, $get, $add, $remove, $set)
    {
        $related = $this->getEntityMock($related);
        $related
            ->expects($this->at(0))
            ->method($set)
            ->will($this->returnSelf())
            ->with($this->get($entity));
        $related
            ->expects($this->at(1))
            ->method($set)
            ->will($this->returnSelf())
            ->with(null);
        $this->assertEmpty($this->call($entity, $get)->toArray());

        // add
        $this->assertEquals($this->get($entity), $this->call($entity, $add, $related));
        $this->assertEquals($this->get($entity), $this->call($entity, $add, $related));
        /* @var $coll \Doctrine\Common\Collections\ArrayCollection */
        $coll = $this->call($entity, $get);
        $this->assertEquals(1, $coll->count());
        $this->assertEquals($related, $coll->first());

        // remove
        $this->assertEquals($this->get($entity), $this->call($entity, $remove, $related));
        $this->assertEmpty($this->call($entity, $get)->toArray());
    }

    /**
     * Get methods many to many
     *
     * @return array
     */
    public function getMethodsManyToMany()
    {
        return [
            [
                'genre',
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item'),
                'getItems',
                'addItem',
                'removeItem',
                'addGenre',
                'removeGenre'
            ],
            [
                'item',
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Genre'),
                'getGenres',
                'addGenre',
                'removeGenre',
                'addItem',
                'removeItem'
            ],
            [
                'item',
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Label'),
                'getLabels',
                'addLabel',
                'removeLabel',
                'addItem',
                'removeItem'
            ],
            [
                'label',
                $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item'),
                'getItems',
                'addItem',
                'removeItem',
                'addLabel',
                'removeLabel'
            ],
        ];
    }

    /**
     * Test many to many
     *
     * @dataProvider getMethodsManyToMany
     *
     * @param string $entity
     * @param \PHPUnit_Framework_MockObject_MockObject $related
     * @param string $get
     * @param string $add
     * @param string $remove
     * @param string $related_add
     * @param string $related_remove
     */
    public function testManyToMany(
        $entity,
        \PHPUnit_Framework_MockObject_MockObject $related,
        $get,
        $add,
        $remove,
        $related_add,
        $related_remove
    ) {
        $related
            ->expects($this->once())
            ->method($related_add)
            ->willReturnSelf()
            ->with($this->get($entity));
        $related
            ->expects($this->once())
            ->method($related_remove)
            ->willReturnSelf()
            ->with($this->get($entity));
        $this->assertEmpty($this->call($entity, $get));

        // add
        $this->assertEquals($this->get($entity), $this->call($entity, $add, $related));
        $this->assertEquals($this->get($entity), $this->call($entity, $add, $related));
        /* @var $coll \Doctrine\Common\Collections\ArrayCollection */
        $coll = $this->call($entity, $get);
        $this->assertEquals(1, $coll->count());
        $this->assertEquals($related, $coll->first());

        // remove
        $this->assertEquals($this->get($entity), $this->call($entity, $remove, $related));
        $this->assertEmpty($this->call($entity, $get));
    }

    /**
     * Get methods many to one
     *
     * @return array
     */
    public function getMethodsManyToOne()
    {
        return [
            [
                'image',
                '\AnimeDb\Bundle\CatalogBundle\Entity\Item',
                'getItem',
                'setItem',
                'addImage',
                'removeImage'
            ],
            [
                'item',
                '\AnimeDb\Bundle\CatalogBundle\Entity\Type',
                'getType',
                'setType',
                'addItem',
                'removeItem'
            ],
            [
                'item',
                '\AnimeDb\Bundle\CatalogBundle\Entity\Country',
                'getCountry',
                'setCountry',
                'addItem',
                'removeItem'
            ],
            [
                'item',
                '\AnimeDb\Bundle\CatalogBundle\Entity\Storage',
                'getStorage',
                'setStorage',
                'addItem',
                'removeItem'
            ],
            [
                'item',
                '\AnimeDb\Bundle\CatalogBundle\Entity\Studio',
                'getStudio',
                'setStudio',
                'addItem',
                'removeItem'
            ],
            [
                'name',
                '\AnimeDb\Bundle\CatalogBundle\Entity\Item',
                'getItem',
                'setItem',
                'addName',
                'removeName'
            ],
            [
                'source',
                '\AnimeDb\Bundle\CatalogBundle\Entity\Item',
                'getItem',
                'setItem',
                'addSource',
                'removeSource'
            ],
        ];
    }

    /**
     * Test many to one
     *
     * @dataProvider getMethodsManyToOne
     *
     * @param string $entity
     * @param string $related
     * @param string $get
     * @param string $set
     * @param string $add
     * @param string $remove
     */
    public function testManyToOne($entity, $related, $get, $set, $add, $remove)
    {
        $related1 = $this->getMock($related);
        $related2 = $this->getMock($related);
        $related1
            ->expects($this->once())
            ->method($add)
            ->with($this->get($entity))
            ->willReturnSelf();
        $related1
            ->expects($this->once())
            ->method($remove)
            ->with($this->get($entity))
            ->willReturnSelf();
        $related2
            ->expects($this->once())
            ->method($add)
            ->with($this->get($entity))
            ->willReturnSelf();
        $related2
            ->expects($this->never())
            ->method($remove);
        $this->assertNull($this->call($entity, $get));

        // add
        $this->assertEquals($this->get($entity), $this->call($entity, $set, $related1));
        $this->assertEquals($this->get($entity), $this->call($entity, $set, $related1));
        $this->assertEquals($related1, $this->call($entity, $get));

        // overwrite related
        $this->assertEquals($this->get($entity), $this->call($entity, $set, $related2));
        $this->assertEquals($related2, $this->call($entity, $get));
    }
}
