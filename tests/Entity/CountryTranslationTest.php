<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Entity;

use AnimeDb\Bundle\CatalogBundle\Entity\CountryTranslation;

/**
 * Test country translation.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class CountryTranslationTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $trans = new CountryTranslation('my_locale', 'my_field', 'my_value');
        $this->assertEquals('my_locale', $trans->getLocale());
        $this->assertEquals('my_field', $trans->getField());
        $this->assertEquals('my_value', $trans->getContent());
    }
}
