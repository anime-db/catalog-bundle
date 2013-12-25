<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131225130333_AddItemStudio extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // create temp table from new structure
        $this->addSql('CREATE TABLE "_new" (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            type VARCHAR(16) DEFAULT NULL,
            country VARCHAR(2) DEFAULT NULL,
            storage INTEGER DEFAULT NULL,
            studio INTEGER DEFAULT NULL,
            name VARCHAR(256) NOT NULL,
            date_start DATE NOT NULL,
            date_end DATE DEFAULT NULL,
            duration INTEGER DEFAULT NULL,
            summary TEXT DEFAULT NULL,
            path VARCHAR(256) DEFAULT NULL,
            episodes TEXT DEFAULT NULL,
            episodes_number VARCHAR(5) DEFAULT NULL,
            translate VARCHAR(256) DEFAULT NULL,
            file_info TEXT DEFAULT NULL,
            cover VARCHAR(256) DEFAULT NULL,
            rating INTEGER DEFAULT NULL,
            date_add DATETIME NOT NULL,
            date_update DATETIME NOT NULL
        )');
        $this->addSql('
            INSERT INTO
                "_new"
            SELECT
                id,
                type,
                country,
                storage,
                NULL,
                name,
                date_start,
                date_end,
                duration,
                summary,
                path,
                episodes,
                episodes_number,
                translate,
                file_info,
                cover,
                rating,
                date_add,
                date_update
            FROM
                "item"
        ');
        // rename new to origin and drop origin
        $this->addSql('ALTER TABLE item RENAME TO _origin');
        $this->addSql('ALTER TABLE _new RENAME TO item');
        $this->addSql('DROP TABLE _origin');

        // create index
        $this->addSql('CREATE INDEX item_country_idx ON item (country);');
        $this->addSql('CREATE INDEX item_storage_idx ON item (storage);');
        $this->addSql('CREATE INDEX item_type_idx ON item (type)');
        $this->addSql('CREATE INDEX item_rating_idx ON item (rating)');
        $this->addSql('CREATE INDEX item_studio_idx ON item (studio)');

        // create studio table
        $this->createTableStudio($schema);
        $this->addDataStudio();
    }

    public function down(Schema $schema)
    {
        // create temp table from origin structure
        $this->addSql('CREATE TABLE "_new" (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            type VARCHAR(16) DEFAULT NULL,
            country VARCHAR(2) DEFAULT NULL,
            storage INTEGER DEFAULT NULL,
            name VARCHAR(256) NOT NULL,
            date_start DATE NOT NULL,
            date_end DATE DEFAULT NULL,
            duration INTEGER DEFAULT NULL,
            summary TEXT DEFAULT NULL,
            path VARCHAR(256) DEFAULT NULL,
            episodes TEXT DEFAULT NULL,
            episodes_number VARCHAR(5) DEFAULT NULL,
            translate VARCHAR(256) DEFAULT NULL,
            file_info TEXT DEFAULT NULL,
            cover VARCHAR(256) DEFAULT NULL,
            rating INTEGER DEFAULT NULL,
            date_add DATETIME NOT NULL,
            date_update DATETIME NOT NULL
        )');
        $this->addSql('
            INSERT INTO
                "_new"
            SELECT
                id,
                type,
                country,
                storage,
                name,
                date_start,
                date_end,
                duration,
                summary,
                path,
                episodes,
                episodes_number,
                translate,
                file_info,
                cover,
                rating,
                date_add,
                date_update
            FROM
                "item"
        ');
        // rename new to origin and drop origin
        $this->addSql('ALTER TABLE item RENAME TO _origin');
        $this->addSql('ALTER TABLE _new RENAME TO item');
        $this->addSql('DROP TABLE _origin');

        // create index
        $this->addSql('CREATE INDEX item_country_idx ON item (country);');
        $this->addSql('CREATE INDEX item_storage_idx ON item (storage);');
        $this->addSql('CREATE INDEX item_type_idx ON item (type)');
        $this->addSql('CREATE INDEX item_rating_idx ON item (rating)');

        // drop table
        $schema->dropTable('studio');
    }

    protected function createTableStudio(Schema $schema)
    {
        $this->addSql('CREATE TABLE "studio" (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            name VARCHAR(128) NOT NULL,
            unified_name VARCHAR(128) NOT NULL
        )');
    }

    protected function addDataStudio()
    {
        $studios = [
            'A Squared Entertainment',
            'A-1 Pictures',
            'A. Film',
            'A.C.G.T',
            'Aardman Animations',
            'Ace & Son Moving Picture Co., LLC',
            'Act3animation',
            'Adelaide Productions',
            'AIC',
            'Ajia-do',
            'AKOM',
            'Ánima Estudios',
            'Animafilm',
            'Animal Logic',
            'Animation Collective',
            'Animax Entertainment',
            'Animonsta Studios',
            'Anzovin Studio',
            'APPP',
            'Arc Productions',
            'Arms Corporation',
            'Artland',
            'Asahi Production',
            'Atomic Cartoons',
            'Augenblick Studios',
            'Bagdasarian Productions',
            'Bee Train',
            'Bent Image Lab',
            'Big Bad Boo',
            'Big Idea Productions',
            'Blue Sky Studios',
            'Blue-Zoo',
            'bolexbrothers',
            'Bones',
            'Boulder Media Limited',
            'Brain\'s Base',
            'BRB International',
            'Brown Bag Films',
            'BUG',
            'Bullwinkle Studios',
            'Buzzco Associates',
            'Callicore',
            'Cartoon Network Studios',
            'Cartoon Pizza/Jumbo Pictures',
            'Cartoon Saloon',
            'Cellmin Animation Studio',
            'Charlex',
            'CinéGroupe',
            'Clockwork Zoo Animation',
            'Collingwood O\'Hare Entertainment',
            'Creative Capers Entertainment',
            'Creative Power Entertaining',
            'Crest Animation Studios',
            'Crew972',
            'Cuckoo\'s Nest Studio',
            'Cuppa Coffee Studio',
            'The Curiosity Company',
            'Curious Pictures',
            'Daume',
            'David Production',
            'Def2shoot',
            'DHX Media',
            'Digital Frontier',
            'Diomedea',
            'DisneyToon Studios',
            'Dogakobo',
            'Don Bluth Films, Inc.',
            'Dong Woo Animation',
            'DR Movie',
            'DreamWorks Animation',
            'Dygra Films',
            'Eiken',
            'EMation',
            'Feel',
            'Felix the Cat Productions',
            'Film Roman, Inc.',
            'Fine Arts Films',
            'Folimage',
            'Fox Animation Studios',
            'Fred Wolf Films Dublin',
            'Frederator Studios',
            'Future Thought Productions',
            'Fuzzy Door Productions',
            'Gainax',
            'Gallop',
            'Global Mechanic',
            'GoHands',
            'Gonzo',
            'GreenFrog Studio',
            'Green Gold Animation',
            'Guru Studios',
            'H5',
            'Hanho Heung-Up',
            'Head-Gear Animation',
            'Hong Ying Animation',
            'Ilion Animation Studios',
            'Illumination Entertainment',
            'Imagi Animation Studios',
            'Imagin',
            'Industrial Light & Magic',
            'J.C.Staff',
            'Janimation',
            'JibJab',
            'John Lemmon Films',
            'Kandor Graphics',
            'Khara',
            'Kharabeesh',
            'Kinema Citrus',
            'Klasky Csupo',
            'Koko Enterprises',
            'KRU Studios',
            'Kyoto Animation',
            'LAIKA',
            'Lambie-Nairn',
            'Les\' Copaque Production',
            'Light Chaser Animation Studios',
            'Littlenobody',
            'Lucasfilm Animation',
            'MAAC India Animation',
            'Mac Guff',
            'Madhouse',
            'Magic Bus',
            'Manglobe',
            'March Entertainment',
            'Marvel Animation Studios',
            'Marwah Films & Video Studios',
            'Marza Animation Planet',
            'Melnitsa Animation Studio',
            'Metro-Goldwyn-Mayer Animation',
            'Mike Young Productions',
            'Millimages',
            'Mirari Films',
            'Mondo Mini Shows',
            'Mondo TV',
            'Mook Animation',
            'Morphia',
            'Mushi Production',
            'National Film Board of Canada',
            'Nelvana',
            'Nickelodeon Animation Studios',
            'Nippon Animation',
            'Nomad',
            'O Entertainment/Omation Animation Studio',
            'Oeuvreglobe/Oeuvreglobe Studios',
            'Oh! Production',
            'OLM, Inc.',
            'Ordet',
            'Oriental DreamWorks',
            'P.A. Works',
            'Pacific Data Images',
            'PannóniaFilm',
            'Paramount Animation',
            'Pentamedia Graphics',
            'The People\'s Republic of Animation',
            'Pierrot',
            'Piranha NYC',
            'Pixar',
            'Plus One Animation',
            'Polygon Pictures',
            'Post Amazers',
            'Powerhouse Animation Studios, Inc.',
            'Premavision/Clokey Productions',
            'Production I.G',
            'Production Reed',
            'Radicial Axis',
            'Rainbow S.r.l.',
            'Rainmaker Digital Effects',
            'Reel FX',
            'Renegade Animation',
            'Rhythm and Hues Studios',
            'Richard Williams Animation',
            'RingTales',
            'Rough Draft Studios',
            'Rubicon Group Holding',
            'Sae Rom',
            'Satelight',
            'Sav! The World Productions',
            'Savage Studios Ltd.',
            'Se-ma-for',
            'Seven Arcs',
            'Shademaker Productions',
            'Shaft',
            'Shanghai Animation Film Studio',
            'Shin-Ei Animation',
            'Silver Link',
            'Six Point Harness',
            'Škola Animiranog Filma',
            'Skycron',
            'Smallfilms',
            'Sony Pictures Animation',
            'Soup2Nuts',
            'Soyuzmultfilm',
            'Sparx*',
            'Spy Pictures',
            'Start Anima',
            'Stretch Films',
            'Studio 4°C',
            'Studio Comet',
            'Studio DEEN',
            'Studio Fantasia',
            'Studio Ghibli',
            'Studio Gokumi',
            'Studio Hibari',
            'Studio Mir',
            'Sumo Dojo',
            'Sunrise',
            'Sunwoo Entertainment',
            'SynergySP',
            'Tatsunoko Productions',
            'Teletoon Canada',
            'Tezuka Productions',
            'Tim Burton Animation Co.',
            'Titmouse',
            'TMS Entertainment',
            'TNK',
            'Toei Animation',
            'Toon City',
            'Toondra',
            'Toonz',
            'TransTales Entertainment',
            'Trigger',
            'Triggerfish Animation Studios',
            'TYO Animations',
            'Ufotable',
            'Universal Animation Studios',
            'Vanguard Animation',
            'Varga Studio',
            'Vasoon Animation',
            'Walt Disney Animation Studios',
            'Walt Disney Television Animation',
            'Wang Film Productions',
            'Warner Bros. Animation',
            'White Fox',
            'W!LDBRAIN',
            'Williams Street Studios',
            'Wit Studio',
            'Wizart Animation',
            'Worker Studio',
            'Xebec',
            'Xilam',
            'Xyzoo Animation',
            'Yowza! Animation',
            'Zagreb school of animated films',
            'Zexcs',
            'Zinkia Entertainment',
            'Sharp Image Animation'
        ];

        foreach ($studios as $studio) {
            $this->addSql(
                'INSERT INTO "studio" VALUES(NULL, :name, :uname)',
                [
                    'name' => $studio,
                    'uname' => $this->getUnifiedName($studio)
                ]
            );
        }
    }

    protected function getUnifiedName($name)
    {
        return trim(preg_replace('/[^\-a-z0-9]+/i', '_', trim($name)), '_');
    }
}