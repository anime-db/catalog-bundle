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
class Version20130930180819_Init extends AbstractMigration
{
    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::up()
     */
    public function up(Schema $schema)
    {
        // create tables
        $this->createTableImage($schema);
        $this->createTableType($schema);
        $this->createTableName($schema);
        $this->createTableItemsGenres($schema);
        $this->createTableSource($schema);
        $this->createTableCountry($schema);
        $this->createTableGenre($schema);
        $this->createTableExtTranslations($schema);
        $this->createTableCountryTranslation($schema);
        $this->createTableStorage($schema);
        $this->createTableItem($schema);

        // clear sqlite sequence
        $this->addSql('DELETE FROM sqlite_sequence WHERE name IN ("image", "name", "source", "genre", "storage", "item")');
        // add sequence for image
        $this->addSql('INSERT INTO "sqlite_sequence" VALUES("image",0)');

        // add data
        $this->addDataTypes();
        $this->addDataCountry();
        $this->addDataGenre();
        $this->addDataExtTranslations();
        $this->addDataCountryTranslation();
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::down()
     */
    public function down(Schema $schema)
    {
        // drop tables
        $schema->dropTable('image');
        $schema->dropTable('type');
        $schema->dropTable('name');
        $schema->dropTable('items_genres');
        $schema->dropTable('source');
        $schema->dropTable('country');
        $schema->dropTable('genre');
        $schema->dropTable('ext_translations');
        $schema->dropTable('country_translation');
        $schema->dropTable('storage');
        $schema->dropTable('item');

        // clear sqlite sequence
        $this->addSql('DELETE FROM sqlite_sequence WHERE name IN ("image", "name", "source", "genre", "storage", "item")');
    }

    protected function createTableImage(Schema $schema)
    {
        $this->addSql('CREATE TABLE image (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            item INTEGER DEFAULT NULL,
            source VARCHAR(256) NOT NULL
        )');
        // add index
        $this->addSql('CREATE INDEX image_item_idx ON image (item)');
    }

    protected function createTableType(Schema $schema)
    {
        $this->addSql('CREATE TABLE type (
            id VARCHAR(16) PRIMARY KEY NOT NULL,
            name VARCHAR(32) NOT NULL
        )');
    }

    protected function createTableName(Schema $schema)
    {
        $this->addSql('CREATE TABLE name (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            item INTEGER DEFAULT NULL,
            name VARCHAR(256) NOT NULL
        )');
        // add index
        $this->addSql('CREATE INDEX name_item_idx ON name (item)');
    }

    protected function createTableItemsGenres(Schema $schema)
    {
        $this->addSql('CREATE TABLE items_genres (
            item_id INTEGER NOT NULL,
            genre_id INTEGER NOT NULL,
            PRIMARY KEY(item_id, genre_id)
        )');
        // add index
        $this->addSql('CREATE INDEX item_genres_item_id_idx ON items_genres (item_id)');
        $this->addSql('CREATE INDEX item_genres_genre_id_idx ON items_genres (genre_id)');
    }

    protected function createTableSource(Schema $schema)
    {
        $this->addSql('CREATE TABLE source (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            item INTEGER DEFAULT NULL,
            url VARCHAR(256) NOT NULL
        )');
        // add index
        $this->addSql('CREATE INDEX source_item_idx ON source (item)');
    }

    protected function createTableCountry(Schema $schema)
    {
        $this->addSql('CREATE TABLE country (
            id VARCHAR(2) PRIMARY KEY NOT NULL,
            name VARCHAR(16) NOT NULL
        )');
    }

    protected function createTableGenre(Schema $schema)
    {
        $this->addSql('CREATE TABLE genre (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            name VARCHAR(16) NOT NULL
        )');
    }

    protected function createTableExtTranslations(Schema $schema)
    {
        $this->addSql('CREATE TABLE ext_translations (
            id INTEGER NOT NULL,
            locale VARCHAR(8) NOT NULL,
            object_class VARCHAR(255) NOT NULL,
            field VARCHAR(32) NOT NULL,
            foreign_key VARCHAR(64) NOT NULL,
            content TEXT DEFAULT NULL,
            PRIMARY KEY(id)
        )');
        // add index
        $this->addSql('CREATE INDEX translations_lookup_idx ON ext_translations (locale, object_class, foreign_key)');
        $this->addSql('CREATE UNIQUE INDEX lookup_unique_idx ON ext_translations (locale, object_class, field, foreign_key)');
    }

    protected function createTableCountryTranslation(Schema $schema)
    {
        $this->addSql('CREATE TABLE country_translation (
            id INTEGER NOT NULL,
            object_id VARCHAR(2) DEFAULT NULL,
            locale VARCHAR(8) NOT NULL,
            field VARCHAR(32) NOT NULL,
            content TEXT DEFAULT NULL,
            PRIMARY KEY(id)
        )');
        // add index
        $this->addSql('CREATE INDEX country_translation_object_id_idx ON country_translation (object_id)');
        $this->addSql('CREATE UNIQUE INDEX country_translation_idx ON country_translation (locale, object_id, field)');
    }

    protected function createTableStorage(Schema $schema)
    {
        $this->addSql('CREATE TABLE "storage" (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            name VARCHAR(128) NOT NULL,
            description TEXT NOT NULL,
            type VARCHAR(16) NOT NULL,
            path TEXT DEFAULT NULL
        )');
    }

    protected function createTableItem(Schema $schema)
    {
        $this->addSql('CREATE TABLE "item"  (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            type VARCHAR(16) DEFAULT NULL,
            manufacturer VARCHAR(2) DEFAULT NULL,
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
            date_add DATETIME NOT NULL,
            date_update DATETIME NOT NULL
        )');
        // add index
        $this->addSql('CREATE INDEX item_manufacturer_idx ON item (manufacturer)');
        $this->addSql('CREATE INDEX item_storage_idx ON item (storage)');
        $this->addSql('CREATE INDEX item_type_idx ON item (type)');
    }

    protected function addDataTypes()
    {
        $this->addSql('INSERT INTO "type" VALUES("feature","Feature")');
        $this->addSql('INSERT INTO "type" VALUES("featurette","Featurette")');
        $this->addSql('INSERT INTO "type" VALUES("ona","ONA")');
        $this->addSql('INSERT INTO "type" VALUES("ova","OVA")');
        $this->addSql('INSERT INTO "type" VALUES("tv","TV")');
        $this->addSql('INSERT INTO "type" VALUES("special","TV-special")');
        $this->addSql('INSERT INTO "type" VALUES("music","Music video")');
        $this->addSql('INSERT INTO "type" VALUES("commercial","Commercial")');
    }

    protected function addDataCountry()
    {
        $this->addSql('INSERT INTO "country" VALUES("AF","Afghanistan")');
        $this->addSql('INSERT INTO "country" VALUES("AL","Albania")');
        $this->addSql('INSERT INTO "country" VALUES("DZ","Algeria")');
        $this->addSql('INSERT INTO "country" VALUES("AS","American Samoa")');
        $this->addSql('INSERT INTO "country" VALUES("AD","Andorra")');
        $this->addSql('INSERT INTO "country" VALUES("AO","Angola")');
        $this->addSql('INSERT INTO "country" VALUES("AI","Anguilla")');
        $this->addSql('INSERT INTO "country" VALUES("AQ","Antarctica")');
        $this->addSql('INSERT INTO "country" VALUES("AG","Antigua and Barbuda")');
        $this->addSql('INSERT INTO "country" VALUES("AR","Argentina")');
        $this->addSql('INSERT INTO "country" VALUES("AM","Armenia")');
        $this->addSql('INSERT INTO "country" VALUES("AW","Aruba")');
        $this->addSql('INSERT INTO "country" VALUES("AU","Australia")');
        $this->addSql('INSERT INTO "country" VALUES("AT","Austria")');
        $this->addSql('INSERT INTO "country" VALUES("AZ","Azerbaijan")');
        $this->addSql('INSERT INTO "country" VALUES("BS","Bahamas")');
        $this->addSql('INSERT INTO "country" VALUES("BH","Bahrain")');
        $this->addSql('INSERT INTO "country" VALUES("BD","Bangladesh")');
        $this->addSql('INSERT INTO "country" VALUES("BB","Barbados")');
        $this->addSql('INSERT INTO "country" VALUES("BY","Belarus")');
        $this->addSql('INSERT INTO "country" VALUES("BE","Belgium")');
        $this->addSql('INSERT INTO "country" VALUES("BZ","Belize")');
        $this->addSql('INSERT INTO "country" VALUES("BJ","Benin")');
        $this->addSql('INSERT INTO "country" VALUES("BM","Bermuda")');
        $this->addSql('INSERT INTO "country" VALUES("BT","Bhutan")');
        $this->addSql('INSERT INTO "country" VALUES("BO","Bolivia")');
        $this->addSql('INSERT INTO "country" VALUES("BA","Bosnia and Herzegovina")');
        $this->addSql('INSERT INTO "country" VALUES("BW","Botswana")');
        $this->addSql('INSERT INTO "country" VALUES("BV","Bouvet Island")');
        $this->addSql('INSERT INTO "country" VALUES("BR","Brazil")');
        $this->addSql('INSERT INTO "country" VALUES("BQ","British Antarctic Territory")');
        $this->addSql('INSERT INTO "country" VALUES("IO","British Indian Ocean Territory")');
        $this->addSql('INSERT INTO "country" VALUES("VG","British Virgin Islands")');
        $this->addSql('INSERT INTO "country" VALUES("BN","Brunei")');
        $this->addSql('INSERT INTO "country" VALUES("BG","Bulgaria")');
        $this->addSql('INSERT INTO "country" VALUES("BF","Burkina Faso")');
        $this->addSql('INSERT INTO "country" VALUES("BI","Burundi")');
        $this->addSql('INSERT INTO "country" VALUES("KH","Cambodia")');
        $this->addSql('INSERT INTO "country" VALUES("CM","Cameroon")');
        $this->addSql('INSERT INTO "country" VALUES("CA","Canada")');
        $this->addSql('INSERT INTO "country" VALUES("CT","Canton and Enderbury Islands")');
        $this->addSql('INSERT INTO "country" VALUES("CV","Cape Verde")');
        $this->addSql('INSERT INTO "country" VALUES("KY","Cayman Islands")');
        $this->addSql('INSERT INTO "country" VALUES("CF","Central African Republic")');
        $this->addSql('INSERT INTO "country" VALUES("TD","Chad")');
        $this->addSql('INSERT INTO "country" VALUES("CL","Chile")');
        $this->addSql('INSERT INTO "country" VALUES("CN","China")');
        $this->addSql('INSERT INTO "country" VALUES("CX","Christmas Island")');
        $this->addSql('INSERT INTO "country" VALUES("CC","Cocos (Keeling) Islands")');
        $this->addSql('INSERT INTO "country" VALUES("CO","Colombia")');
        $this->addSql('INSERT INTO "country" VALUES("KM","Comoros")');
        $this->addSql('INSERT INTO "country" VALUES("CG","Congo - Brazzaville")');
        $this->addSql('INSERT INTO "country" VALUES("CD","Congo - Kinshasa")');
        $this->addSql('INSERT INTO "country" VALUES("CK","Cook Islands")');
        $this->addSql('INSERT INTO "country" VALUES("CR","Costa Rica")');
        $this->addSql('INSERT INTO "country" VALUES("HR","Croatia")');
        $this->addSql('INSERT INTO "country" VALUES("CU","Cuba")');
        $this->addSql('INSERT INTO "country" VALUES("CY","Cyprus")');
        $this->addSql('INSERT INTO "country" VALUES("CZ","Czech Republic")');
        $this->addSql('INSERT INTO "country" VALUES("CI","Côte d’Ivoire")');
        $this->addSql('INSERT INTO "country" VALUES("DK","Denmark")');
        $this->addSql('INSERT INTO "country" VALUES("DJ","Djibouti")');
        $this->addSql('INSERT INTO "country" VALUES("DM","Dominica")');
        $this->addSql('INSERT INTO "country" VALUES("DO","Dominican Republic")');
        $this->addSql('INSERT INTO "country" VALUES("NQ","Dronning Maud Land")');
        $this->addSql('INSERT INTO "country" VALUES("DD","East Germany")');
        $this->addSql('INSERT INTO "country" VALUES("EC","Ecuador")');
        $this->addSql('INSERT INTO "country" VALUES("EG","Egypt")');
        $this->addSql('INSERT INTO "country" VALUES("SV","El Salvador")');
        $this->addSql('INSERT INTO "country" VALUES("GQ","Equatorial Guinea")');
        $this->addSql('INSERT INTO "country" VALUES("ER","Eritrea")');
        $this->addSql('INSERT INTO "country" VALUES("EE","Estonia")');
        $this->addSql('INSERT INTO "country" VALUES("ET","Ethiopia")');
        $this->addSql('INSERT INTO "country" VALUES("FK","Falkland Islands")');
        $this->addSql('INSERT INTO "country" VALUES("FO","Faroe Islands")');
        $this->addSql('INSERT INTO "country" VALUES("FJ","Fiji")');
        $this->addSql('INSERT INTO "country" VALUES("FI","Finland")');
        $this->addSql('INSERT INTO "country" VALUES("FR","France")');
        $this->addSql('INSERT INTO "country" VALUES("GF","French Guiana")');
        $this->addSql('INSERT INTO "country" VALUES("PF","French Polynesia")');
        $this->addSql('INSERT INTO "country" VALUES("TF","French Southern Territories")');
        $this->addSql('INSERT INTO "country" VALUES("FQ","French Southern and Antarctic Territories")');
        $this->addSql('INSERT INTO "country" VALUES("GA","Gabon")');
        $this->addSql('INSERT INTO "country" VALUES("GM","Gambia")');
        $this->addSql('INSERT INTO "country" VALUES("GE","Georgia")');
        $this->addSql('INSERT INTO "country" VALUES("DE","Germany")');
        $this->addSql('INSERT INTO "country" VALUES("GH","Ghana")');
        $this->addSql('INSERT INTO "country" VALUES("GI","Gibraltar")');
        $this->addSql('INSERT INTO "country" VALUES("GR","Greece")');
        $this->addSql('INSERT INTO "country" VALUES("GL","Greenland")');
        $this->addSql('INSERT INTO "country" VALUES("GD","Grenada")');
        $this->addSql('INSERT INTO "country" VALUES("GP","Guadeloupe")');
        $this->addSql('INSERT INTO "country" VALUES("GU","Guam")');
        $this->addSql('INSERT INTO "country" VALUES("GT","Guatemala")');
        $this->addSql('INSERT INTO "country" VALUES("GG","Guernsey")');
        $this->addSql('INSERT INTO "country" VALUES("GN","Guinea")');
        $this->addSql('INSERT INTO "country" VALUES("GW","Guinea-Bissau")');
        $this->addSql('INSERT INTO "country" VALUES("GY","Guyana")');
        $this->addSql('INSERT INTO "country" VALUES("HT","Haiti")');
        $this->addSql('INSERT INTO "country" VALUES("HM","Heard Island and McDonald Islands")');
        $this->addSql('INSERT INTO "country" VALUES("HN","Honduras")');
        $this->addSql('INSERT INTO "country" VALUES("HK","Hong Kong SAR China")');
        $this->addSql('INSERT INTO "country" VALUES("HU","Hungary")');
        $this->addSql('INSERT INTO "country" VALUES("IS","Iceland")');
        $this->addSql('INSERT INTO "country" VALUES("IN","India")');
        $this->addSql('INSERT INTO "country" VALUES("ID","Indonesia")');
        $this->addSql('INSERT INTO "country" VALUES("IR","Iran")');
        $this->addSql('INSERT INTO "country" VALUES("IQ","Iraq")');
        $this->addSql('INSERT INTO "country" VALUES("IE","Ireland")');
        $this->addSql('INSERT INTO "country" VALUES("IM","Isle of Man")');
        $this->addSql('INSERT INTO "country" VALUES("IL","Israel")');
        $this->addSql('INSERT INTO "country" VALUES("IT","Italy")');
        $this->addSql('INSERT INTO "country" VALUES("JM","Jamaica")');
        $this->addSql('INSERT INTO "country" VALUES("JP","Japan")');
        $this->addSql('INSERT INTO "country" VALUES("JE","Jersey")');
        $this->addSql('INSERT INTO "country" VALUES("JT","Johnston Island")');
        $this->addSql('INSERT INTO "country" VALUES("JO","Jordan")');
        $this->addSql('INSERT INTO "country" VALUES("KZ","Kazakhstan")');
        $this->addSql('INSERT INTO "country" VALUES("KE","Kenya")');
        $this->addSql('INSERT INTO "country" VALUES("KI","Kiribati")');
        $this->addSql('INSERT INTO "country" VALUES("KW","Kuwait")');
        $this->addSql('INSERT INTO "country" VALUES("KG","Kyrgyzstan")');
        $this->addSql('INSERT INTO "country" VALUES("LA","Laos")');
        $this->addSql('INSERT INTO "country" VALUES("LV","Latvia")');
        $this->addSql('INSERT INTO "country" VALUES("LB","Lebanon")');
        $this->addSql('INSERT INTO "country" VALUES("LS","Lesotho")');
        $this->addSql('INSERT INTO "country" VALUES("LR","Liberia")');
        $this->addSql('INSERT INTO "country" VALUES("LY","Libya")');
        $this->addSql('INSERT INTO "country" VALUES("LI","Liechtenstein")');
        $this->addSql('INSERT INTO "country" VALUES("LT","Lithuania")');
        $this->addSql('INSERT INTO "country" VALUES("LU","Luxembourg")');
        $this->addSql('INSERT INTO "country" VALUES("MO","Macau SAR China")');
        $this->addSql('INSERT INTO "country" VALUES("MK","Macedonia")');
        $this->addSql('INSERT INTO "country" VALUES("MG","Madagascar")');
        $this->addSql('INSERT INTO "country" VALUES("MW","Malawi")');
        $this->addSql('INSERT INTO "country" VALUES("MY","Malaysia")');
        $this->addSql('INSERT INTO "country" VALUES("MV","Maldives")');
        $this->addSql('INSERT INTO "country" VALUES("ML","Mali")');
        $this->addSql('INSERT INTO "country" VALUES("MT","Malta")');
        $this->addSql('INSERT INTO "country" VALUES("MH","Marshall Islands")');
        $this->addSql('INSERT INTO "country" VALUES("MQ","Martinique")');
        $this->addSql('INSERT INTO "country" VALUES("MR","Mauritania")');
        $this->addSql('INSERT INTO "country" VALUES("MU","Mauritius")');
        $this->addSql('INSERT INTO "country" VALUES("YT","Mayotte")');
        $this->addSql('INSERT INTO "country" VALUES("FX","Metropolitan France")');
        $this->addSql('INSERT INTO "country" VALUES("MX","Mexico")');
        $this->addSql('INSERT INTO "country" VALUES("FM","Micronesia")');
        $this->addSql('INSERT INTO "country" VALUES("MI","Midway Islands")');
        $this->addSql('INSERT INTO "country" VALUES("MD","Moldova")');
        $this->addSql('INSERT INTO "country" VALUES("MC","Monaco")');
        $this->addSql('INSERT INTO "country" VALUES("MN","Mongolia")');
        $this->addSql('INSERT INTO "country" VALUES("ME","Montenegro")');
        $this->addSql('INSERT INTO "country" VALUES("MS","Montserrat")');
        $this->addSql('INSERT INTO "country" VALUES("MA","Morocco")');
        $this->addSql('INSERT INTO "country" VALUES("MZ","Mozambique")');
        $this->addSql('INSERT INTO "country" VALUES("MM","Myanmar (Burma)")');
        $this->addSql('INSERT INTO "country" VALUES("NA","Namibia")');
        $this->addSql('INSERT INTO "country" VALUES("NR","Nauru")');
        $this->addSql('INSERT INTO "country" VALUES("NP","Nepal")');
        $this->addSql('INSERT INTO "country" VALUES("NL","Netherlands")');
        $this->addSql('INSERT INTO "country" VALUES("AN","Netherlands Antilles")');
        $this->addSql('INSERT INTO "country" VALUES("NT","Neutral Zone")');
        $this->addSql('INSERT INTO "country" VALUES("NC","New Caledonia")');
        $this->addSql('INSERT INTO "country" VALUES("NZ","New Zealand")');
        $this->addSql('INSERT INTO "country" VALUES("NI","Nicaragua")');
        $this->addSql('INSERT INTO "country" VALUES("NE","Niger")');
        $this->addSql('INSERT INTO "country" VALUES("NG","Nigeria")');
        $this->addSql('INSERT INTO "country" VALUES("NU","Niue")');
        $this->addSql('INSERT INTO "country" VALUES("NF","Norfolk Island")');
        $this->addSql('INSERT INTO "country" VALUES("KP","North Korea")');
        $this->addSql('INSERT INTO "country" VALUES("VD","North Vietnam")');
        $this->addSql('INSERT INTO "country" VALUES("MP","Northern Mariana Islands")');
        $this->addSql('INSERT INTO "country" VALUES("NO","Norway")');
        $this->addSql('INSERT INTO "country" VALUES("OM","Oman")');
        $this->addSql('INSERT INTO "country" VALUES("PC","Pacific Islands Trust Territory")');
        $this->addSql('INSERT INTO "country" VALUES("PK","Pakistan")');
        $this->addSql('INSERT INTO "country" VALUES("PW","Palau")');
        $this->addSql('INSERT INTO "country" VALUES("PS","Palestinian Territories")');
        $this->addSql('INSERT INTO "country" VALUES("PA","Panama")');
        $this->addSql('INSERT INTO "country" VALUES("PZ","Panama Canal Zone")');
        $this->addSql('INSERT INTO "country" VALUES("PG","Papua New Guinea")');
        $this->addSql('INSERT INTO "country" VALUES("PY","Paraguay")');
        $this->addSql('INSERT INTO "country" VALUES("YD","People`s Democratic Republic of Yemen")');
        $this->addSql('INSERT INTO "country" VALUES("PE","Peru")');
        $this->addSql('INSERT INTO "country" VALUES("PH","Philippines")');
        $this->addSql('INSERT INTO "country" VALUES("PN","Pitcairn Islands")');
        $this->addSql('INSERT INTO "country" VALUES("PL","Poland")');
        $this->addSql('INSERT INTO "country" VALUES("PT","Portugal")');
        $this->addSql('INSERT INTO "country" VALUES("PR","Puerto Rico")');
        $this->addSql('INSERT INTO "country" VALUES("QA","Qatar")');
        $this->addSql('INSERT INTO "country" VALUES("RO","Romania")');
        $this->addSql('INSERT INTO "country" VALUES("RU","Russia")');
        $this->addSql('INSERT INTO "country" VALUES("RW","Rwanda")');
        $this->addSql('INSERT INTO "country" VALUES("RE","Réunion")');
        $this->addSql('INSERT INTO "country" VALUES("BL","Saint Barthélemy")');
        $this->addSql('INSERT INTO "country" VALUES("SH","Saint Helena")');
        $this->addSql('INSERT INTO "country" VALUES("KN","Saint Kitts and Nevis")');
        $this->addSql('INSERT INTO "country" VALUES("LC","Saint Lucia")');
        $this->addSql('INSERT INTO "country" VALUES("MF","Saint Martin")');
        $this->addSql('INSERT INTO "country" VALUES("PM","Saint Pierre and Miquelon")');
        $this->addSql('INSERT INTO "country" VALUES("VC","Saint Vincent and the Grenadines")');
        $this->addSql('INSERT INTO "country" VALUES("WS","Samoa")');
        $this->addSql('INSERT INTO "country" VALUES("SM","San Marino")');
        $this->addSql('INSERT INTO "country" VALUES("SA","Saudi Arabia")');
        $this->addSql('INSERT INTO "country" VALUES("SN","Senegal")');
        $this->addSql('INSERT INTO "country" VALUES("RS","Serbia")');
        $this->addSql('INSERT INTO "country" VALUES("CS","Serbia and Montenegro")');
        $this->addSql('INSERT INTO "country" VALUES("SC","Seychelles")');
        $this->addSql('INSERT INTO "country" VALUES("SL","Sierra Leone")');
        $this->addSql('INSERT INTO "country" VALUES("SG","Singapore")');
        $this->addSql('INSERT INTO "country" VALUES("SK","Slovakia")');
        $this->addSql('INSERT INTO "country" VALUES("SI","Slovenia")');
        $this->addSql('INSERT INTO "country" VALUES("SB","Solomon Islands")');
        $this->addSql('INSERT INTO "country" VALUES("SO","Somalia")');
        $this->addSql('INSERT INTO "country" VALUES("ZA","South Africa")');
        $this->addSql('INSERT INTO "country" VALUES("GS","South Georgia and the South Sandwich Islands")');
        $this->addSql('INSERT INTO "country" VALUES("KR","South Korea")');
        $this->addSql('INSERT INTO "country" VALUES("ES","Spain")');
        $this->addSql('INSERT INTO "country" VALUES("LK","Sri Lanka")');
        $this->addSql('INSERT INTO "country" VALUES("SD","Sudan")');
        $this->addSql('INSERT INTO "country" VALUES("SR","Suriname")');
        $this->addSql('INSERT INTO "country" VALUES("SJ","Svalbard and Jan Mayen")');
        $this->addSql('INSERT INTO "country" VALUES("SZ","Swaziland")');
        $this->addSql('INSERT INTO "country" VALUES("SE","Sweden")');
        $this->addSql('INSERT INTO "country" VALUES("CH","Switzerland")');
        $this->addSql('INSERT INTO "country" VALUES("SY","Syria")');
        $this->addSql('INSERT INTO "country" VALUES("ST","São Tomé and Príncipe")');
        $this->addSql('INSERT INTO "country" VALUES("TW","Taiwan")');
        $this->addSql('INSERT INTO "country" VALUES("TJ","Tajikistan")');
        $this->addSql('INSERT INTO "country" VALUES("TZ","Tanzania")');
        $this->addSql('INSERT INTO "country" VALUES("TH","Thailand")');
        $this->addSql('INSERT INTO "country" VALUES("TL","Timor-Leste")');
        $this->addSql('INSERT INTO "country" VALUES("TG","Togo")');
        $this->addSql('INSERT INTO "country" VALUES("TK","Tokelau")');
        $this->addSql('INSERT INTO "country" VALUES("TO","Tonga")');
        $this->addSql('INSERT INTO "country" VALUES("TT","Trinidad and Tobago")');
        $this->addSql('INSERT INTO "country" VALUES("TN","Tunisia")');
        $this->addSql('INSERT INTO "country" VALUES("TR","Turkey")');
        $this->addSql('INSERT INTO "country" VALUES("TM","Turkmenistan")');
        $this->addSql('INSERT INTO "country" VALUES("TC","Turks and Caicos Islands")');
        $this->addSql('INSERT INTO "country" VALUES("TV","Tuvalu")');
        $this->addSql('INSERT INTO "country" VALUES("UM","U.S. Minor Outlying Islands")');
        $this->addSql('INSERT INTO "country" VALUES("PU","U.S. Miscellaneous Pacific Islands")');
        $this->addSql('INSERT INTO "country" VALUES("VI","U.S. Virgin Islands")');
        $this->addSql('INSERT INTO "country" VALUES("UG","Uganda")');
        $this->addSql('INSERT INTO "country" VALUES("UA","Ukraine")');
        $this->addSql('INSERT INTO "country" VALUES("SU","Union of Soviet Socialist Republics")');
        $this->addSql('INSERT INTO "country" VALUES("AE","United Arab Emirates")');
        $this->addSql('INSERT INTO "country" VALUES("GB","United Kingdom")');
        $this->addSql('INSERT INTO "country" VALUES("US","United States")');
        $this->addSql('INSERT INTO "country" VALUES("ZZ","Unknown or Invalid Region")');
        $this->addSql('INSERT INTO "country" VALUES("UY","Uruguay")');
        $this->addSql('INSERT INTO "country" VALUES("UZ","Uzbekistan")');
        $this->addSql('INSERT INTO "country" VALUES("VU","Vanuatu")');
        $this->addSql('INSERT INTO "country" VALUES("VA","Vatican City")');
        $this->addSql('INSERT INTO "country" VALUES("VE","Venezuela")');
        $this->addSql('INSERT INTO "country" VALUES("VN","Vietnam")');
        $this->addSql('INSERT INTO "country" VALUES("WK","Wake Island")');
        $this->addSql('INSERT INTO "country" VALUES("WF","Wallis and Futuna")');
        $this->addSql('INSERT INTO "country" VALUES("EH","Western Sahara")');
        $this->addSql('INSERT INTO "country" VALUES("YE","Yemen")');
        $this->addSql('INSERT INTO "country" VALUES("ZM","Zambia")');
        $this->addSql('INSERT INTO "country" VALUES("ZW","Zimbabwe")');
        $this->addSql('INSERT INTO "country" VALUES("AX","Åland Islands")');
    }

    protected function addDataGenre()
    {
        $this->addSql('INSERT INTO "genre" VALUES(1,"Adventure")');
        $this->addSql('INSERT INTO "genre" VALUES(2,"Comedy")');
        $this->addSql('INSERT INTO "genre" VALUES(3,"Fantastic")');
        $this->addSql('INSERT INTO "genre" VALUES(4,"Drama")');
        $this->addSql('INSERT INTO "genre" VALUES(5,"Action")');
        $this->addSql('INSERT INTO "genre" VALUES(6,"Martial arts")');
        $this->addSql('INSERT INTO "genre" VALUES(7,"War")');
        $this->addSql('INSERT INTO "genre" VALUES(8,"Detective")');
        $this->addSql('INSERT INTO "genre" VALUES(9,"For children")');
        $this->addSql('INSERT INTO "genre" VALUES(10,"History")');
        $this->addSql('INSERT INTO "genre" VALUES(11,"Mahoe shoujo")');
        $this->addSql('INSERT INTO "genre" VALUES(12,"Meho")');
        $this->addSql('INSERT INTO "genre" VALUES(13,"Mysticism")');
        $this->addSql('INSERT INTO "genre" VALUES(14,"Musical")');
        $this->addSql('INSERT INTO "genre" VALUES(15,"Educational")');
        $this->addSql('INSERT INTO "genre" VALUES(16,"Parody")');
        $this->addSql('INSERT INTO "genre" VALUES(17,"Everyday")');
        $this->addSql('INSERT INTO "genre" VALUES(18,"Police")');
        $this->addSql('INSERT INTO "genre" VALUES(19,"Romance")');
        $this->addSql('INSERT INTO "genre" VALUES(20,"Samurai action")');
        $this->addSql('INSERT INTO "genre" VALUES(21,"Shoujo")');
        $this->addSql('INSERT INTO "genre" VALUES(22,"Shoujo-ai")');
        $this->addSql('INSERT INTO "genre" VALUES(23,"Senen")');
        $this->addSql('INSERT INTO "genre" VALUES(24,"Senen-ai")');
        $this->addSql('INSERT INTO "genre" VALUES(47,"Fable")');
        $this->addSql('INSERT INTO "genre" VALUES(48,"Sport")');
        $this->addSql('INSERT INTO "genre" VALUES(49,"Thriller")');
        $this->addSql('INSERT INTO "genre" VALUES(50,"School")');
        $this->addSql('INSERT INTO "genre" VALUES(51,"Fantasy")');
        $this->addSql('INSERT INTO "genre" VALUES(52,"Erotica")');
        $this->addSql('INSERT INTO "genre" VALUES(53,"Ettie")');
        $this->addSql('INSERT INTO "genre" VALUES(54,"Horror")');
        $this->addSql('INSERT INTO "genre" VALUES(55,"Hentai")');
        $this->addSql('INSERT INTO "genre" VALUES(56,"Urey")');
        $this->addSql('INSERT INTO "genre" VALUES(57,"Yaoi")');
        $this->addSql('INSERT INTO "genre" VALUES(58,"Psychology")');
        $this->addSql('INSERT INTO "genre" VALUES(59,"Apocalyptic fiction")');
        $this->addSql('INSERT INTO "genre" VALUES(60,"Steampunk")');
        $this->addSql('INSERT INTO "genre" VALUES(61,"Mystery play")');
        $this->addSql('INSERT INTO "genre" VALUES(62,"Josei")');
        $this->addSql('INSERT INTO "genre" VALUES(63,"Vampires")');
        $this->addSql('INSERT INTO "genre" VALUES(64,"Cyberpunk")');
        // add sequence
        $this->addSql('INSERT INTO "sqlite_sequence" VALUES("genre",64)');
    }

    protected function addDataExtTranslations()
    {
        $this->addSql('INSERT INTO "ext_translations" VALUES(1,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Type","name","feature","Полнометражный фильм")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(2,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Type","name","featurette","Короткометражный фильм")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(3,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Type","name","ona","ONA")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(4,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Type","name","ova","OVA")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(5,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Type","name","tv","ТВ")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(6,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Type","name","special","ТВ спецвыпуск")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(7,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Type","name","music","Музыкальное видео")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(8,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Type","name","commercial","Рекламный ролик")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(9,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","1","Приключения")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(10,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","2","Комедия")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(11,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","3","Фантастика")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(12,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","4","Драма")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(13,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","5","Боевик")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(14,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","6","Боевые искусства")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(15,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","7","Война")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(16,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","8","Детектив")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(17,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","9","Для детей")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(18,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","10","История")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(19,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","11","Махо-сёдзё")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(20,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","12","Меха")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(21,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","13","Мистика")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(22,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","14","Музыкальный")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(23,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","15","Образовательный")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(24,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","16","Пародия")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(25,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","17","Повседневность")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(26,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","18","Полиция")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(27,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","19","Романтика")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(28,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","20","Самурайский боевик")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(29,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","21","Сёдзё")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(30,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","22","Сёдзё-ай")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(31,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","23","Сёнэн")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(32,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","24","Сёнэн-ай")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(33,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","47","Сказка")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(34,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","48","Спорт")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(35,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","49","Триллер")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(36,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","50","Школа")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(37,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","51","Фэнтези")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(38,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","52","Эротика")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(39,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","53","Этти")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(40,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","54","Ужасы")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(41,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","55","Хентай")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(42,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","56","Юри")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(43,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","57","Яой")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(44,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","58","Психология")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(45,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","59","Постапокалиптика")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(46,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","60","Стимпанк")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(47,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","61","Мистерия")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(48,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","62","Дзёсэй")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(49,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","63","Вампиры")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(50,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","64","Киберпанк")');
    }

    protected function addDataCountryTranslation()
    {
            $this->addSql('INSERT INTO "country_translation" VALUES(1,"AF","en","name","Afghanistan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(2,"AF","ru","name","Афганистан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(3,"AL","en","name","Albania")');
            $this->addSql('INSERT INTO "country_translation" VALUES(4,"AL","ru","name","Албания")');
            $this->addSql('INSERT INTO "country_translation" VALUES(5,"DZ","en","name","Algeria")');
            $this->addSql('INSERT INTO "country_translation" VALUES(6,"DZ","ru","name","Алжир")');
            $this->addSql('INSERT INTO "country_translation" VALUES(7,"AS","en","name","American Samoa")');
            $this->addSql('INSERT INTO "country_translation" VALUES(8,"AS","ru","name","Американское Самоа")');
            $this->addSql('INSERT INTO "country_translation" VALUES(9,"AD","en","name","Andorra")');
            $this->addSql('INSERT INTO "country_translation" VALUES(10,"AD","ru","name","Андорра")');
            $this->addSql('INSERT INTO "country_translation" VALUES(11,"AO","en","name","Angola")');
            $this->addSql('INSERT INTO "country_translation" VALUES(12,"AO","ru","name","Ангола")');
            $this->addSql('INSERT INTO "country_translation" VALUES(13,"AI","en","name","Anguilla")');
            $this->addSql('INSERT INTO "country_translation" VALUES(14,"AI","ru","name","Ангуилла")');
            $this->addSql('INSERT INTO "country_translation" VALUES(15,"AQ","en","name","Antarctica")');
            $this->addSql('INSERT INTO "country_translation" VALUES(16,"AQ","ru","name","Антарктика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(17,"AG","en","name","Antigua and Barbuda")');
            $this->addSql('INSERT INTO "country_translation" VALUES(18,"AG","ru","name","Антигуа и Барбуда")');
            $this->addSql('INSERT INTO "country_translation" VALUES(19,"AR","en","name","Argentina")');
            $this->addSql('INSERT INTO "country_translation" VALUES(20,"AR","ru","name","Аргентина")');
            $this->addSql('INSERT INTO "country_translation" VALUES(21,"AM","en","name","Armenia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(22,"AM","ru","name","Армения")');
            $this->addSql('INSERT INTO "country_translation" VALUES(23,"AW","en","name","Aruba")');
            $this->addSql('INSERT INTO "country_translation" VALUES(24,"AW","ru","name","Аруба")');
            $this->addSql('INSERT INTO "country_translation" VALUES(25,"AU","en","name","Australia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(26,"AU","ru","name","Австралия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(27,"AT","en","name","Austria")');
            $this->addSql('INSERT INTO "country_translation" VALUES(28,"AT","ru","name","Австрия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(29,"AZ","en","name","Azerbaijan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(30,"AZ","ru","name","Азербайджан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(31,"BS","en","name","Bahamas")');
            $this->addSql('INSERT INTO "country_translation" VALUES(32,"BS","ru","name","Багамские острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(33,"BH","en","name","Bahrain")');
            $this->addSql('INSERT INTO "country_translation" VALUES(34,"BH","ru","name","Бахрейн")');
            $this->addSql('INSERT INTO "country_translation" VALUES(35,"BD","en","name","Bangladesh")');
            $this->addSql('INSERT INTO "country_translation" VALUES(36,"BD","ru","name","Бангладеш")');
            $this->addSql('INSERT INTO "country_translation" VALUES(37,"BB","en","name","Barbados")');
            $this->addSql('INSERT INTO "country_translation" VALUES(38,"BB","ru","name","Барбадос")');
            $this->addSql('INSERT INTO "country_translation" VALUES(39,"BY","en","name","Belarus")');
            $this->addSql('INSERT INTO "country_translation" VALUES(40,"BY","ru","name","Беларусь")');
            $this->addSql('INSERT INTO "country_translation" VALUES(41,"BE","en","name","Belgium")');
            $this->addSql('INSERT INTO "country_translation" VALUES(42,"BE","ru","name","Бельгия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(43,"BZ","en","name","Belize")');
            $this->addSql('INSERT INTO "country_translation" VALUES(44,"BZ","ru","name","Белиз")');
            $this->addSql('INSERT INTO "country_translation" VALUES(45,"BJ","en","name","Benin")');
            $this->addSql('INSERT INTO "country_translation" VALUES(46,"BJ","ru","name","Бенин")');
            $this->addSql('INSERT INTO "country_translation" VALUES(47,"BM","en","name","Bermuda")');
            $this->addSql('INSERT INTO "country_translation" VALUES(48,"BM","ru","name","Бермудские Острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(49,"BT","en","name","Bhutan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(50,"BT","ru","name","Бутан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(51,"BO","en","name","Bolivia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(52,"BO","ru","name","Боливия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(53,"BA","en","name","Bosnia and Herzegovina")');
            $this->addSql('INSERT INTO "country_translation" VALUES(54,"BA","ru","name","Босния и Герцеговина")');
            $this->addSql('INSERT INTO "country_translation" VALUES(55,"BW","en","name","Botswana")');
            $this->addSql('INSERT INTO "country_translation" VALUES(56,"BW","ru","name","Ботсвана")');
            $this->addSql('INSERT INTO "country_translation" VALUES(57,"BV","en","name","Bouvet Island")');
            $this->addSql('INSERT INTO "country_translation" VALUES(58,"BV","ru","name","Остров Буве")');
            $this->addSql('INSERT INTO "country_translation" VALUES(59,"BR","en","name","Brazil")');
            $this->addSql('INSERT INTO "country_translation" VALUES(60,"BR","ru","name","Бразилия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(61,"BQ","en","name","British Antarctic Territory")');
            $this->addSql('INSERT INTO "country_translation" VALUES(62,"BQ","ru","name","Британская антарктическая территория")');
            $this->addSql('INSERT INTO "country_translation" VALUES(63,"IO","en","name","British Indian Ocean Territory")');
            $this->addSql('INSERT INTO "country_translation" VALUES(64,"IO","ru","name","Британская территория в Индийском океане")');
            $this->addSql('INSERT INTO "country_translation" VALUES(65,"VG","en","name","British Virgin Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(66,"VG","ru","name","Британские Виргинские Острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(67,"BN","en","name","Brunei")');
            $this->addSql('INSERT INTO "country_translation" VALUES(68,"BN","ru","name","Бруней Даруссалам")');
            $this->addSql('INSERT INTO "country_translation" VALUES(69,"BG","en","name","Bulgaria")');
            $this->addSql('INSERT INTO "country_translation" VALUES(70,"BG","ru","name","Болгария")');
            $this->addSql('INSERT INTO "country_translation" VALUES(71,"BF","en","name","Burkina Faso")');
            $this->addSql('INSERT INTO "country_translation" VALUES(72,"BF","ru","name","Буркина Фасо")');
            $this->addSql('INSERT INTO "country_translation" VALUES(73,"BI","en","name","Burundi")');
            $this->addSql('INSERT INTO "country_translation" VALUES(74,"BI","ru","name","Бурунди")');
            $this->addSql('INSERT INTO "country_translation" VALUES(75,"KH","en","name","Cambodia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(76,"KH","ru","name","Камбоджа")');
            $this->addSql('INSERT INTO "country_translation" VALUES(77,"CM","en","name","Cameroon")');
            $this->addSql('INSERT INTO "country_translation" VALUES(78,"CM","ru","name","Камерун")');
            $this->addSql('INSERT INTO "country_translation" VALUES(79,"CA","en","name","Canada")');
            $this->addSql('INSERT INTO "country_translation" VALUES(80,"CA","ru","name","Канада")');
            $this->addSql('INSERT INTO "country_translation" VALUES(81,"CT","en","name","Canton and Enderbury Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(82,"CT","ru","name","Кантон и Эндербери")');
            $this->addSql('INSERT INTO "country_translation" VALUES(83,"CV","en","name","Cape Verde")');
            $this->addSql('INSERT INTO "country_translation" VALUES(84,"CV","ru","name","Острова Зеленого Мыса")');
            $this->addSql('INSERT INTO "country_translation" VALUES(85,"KY","en","name","Cayman Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(86,"KY","ru","name","Каймановы острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(87,"CF","en","name","Central African Republic")');
            $this->addSql('INSERT INTO "country_translation" VALUES(88,"CF","ru","name","Центрально-Африканская Республика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(89,"TD","en","name","Chad")');
            $this->addSql('INSERT INTO "country_translation" VALUES(90,"TD","ru","name","Чад")');
            $this->addSql('INSERT INTO "country_translation" VALUES(91,"CL","en","name","Chile")');
            $this->addSql('INSERT INTO "country_translation" VALUES(92,"CL","ru","name","Чили")');
            $this->addSql('INSERT INTO "country_translation" VALUES(93,"CN","en","name","China")');
            $this->addSql('INSERT INTO "country_translation" VALUES(94,"CN","ru","name","Китай")');
            $this->addSql('INSERT INTO "country_translation" VALUES(95,"CX","en","name","Christmas Island")');
            $this->addSql('INSERT INTO "country_translation" VALUES(96,"CX","ru","name","Остров Рождества")');
            $this->addSql('INSERT INTO "country_translation" VALUES(97,"CC","en","name","Cocos (Keeling) Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(98,"CC","ru","name","Кокосовые острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(99,"CO","en","name","Colombia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(100,"CO","ru","name","Колумбия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(101,"KM","en","name","Comoros")');
            $this->addSql('INSERT INTO "country_translation" VALUES(102,"KM","ru","name","Коморские Острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(103,"CG","en","name","Congo - Brazzaville")');
            $this->addSql('INSERT INTO "country_translation" VALUES(104,"CG","ru","name","Конго")');
            $this->addSql('INSERT INTO "country_translation" VALUES(105,"CD","en","name","Congo - Kinshasa")');
            $this->addSql('INSERT INTO "country_translation" VALUES(106,"CD","ru","name","Демократическая Республика Конго")');
            $this->addSql('INSERT INTO "country_translation" VALUES(107,"CK","en","name","Cook Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(108,"CK","ru","name","Острова Кука")');
            $this->addSql('INSERT INTO "country_translation" VALUES(109,"CR","en","name","Costa Rica")');
            $this->addSql('INSERT INTO "country_translation" VALUES(110,"CR","ru","name","Коста-Рика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(111,"HR","en","name","Croatia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(112,"HR","ru","name","Хорватия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(113,"CU","en","name","Cuba")');
            $this->addSql('INSERT INTO "country_translation" VALUES(114,"CU","ru","name","Куба")');
            $this->addSql('INSERT INTO "country_translation" VALUES(115,"CY","en","name","Cyprus")');
            $this->addSql('INSERT INTO "country_translation" VALUES(116,"CY","ru","name","Кипр")');
            $this->addSql('INSERT INTO "country_translation" VALUES(117,"CZ","en","name","Czech Republic")');
            $this->addSql('INSERT INTO "country_translation" VALUES(118,"CZ","ru","name","Чешская республика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(119,"CI","en","name","Côte d’Ivoire")');
            $this->addSql('INSERT INTO "country_translation" VALUES(120,"CI","ru","name","Кот д’Ивуар")');
            $this->addSql('INSERT INTO "country_translation" VALUES(121,"DK","en","name","Denmark")');
            $this->addSql('INSERT INTO "country_translation" VALUES(122,"DK","ru","name","Дания")');
            $this->addSql('INSERT INTO "country_translation" VALUES(123,"DJ","en","name","Djibouti")');
            $this->addSql('INSERT INTO "country_translation" VALUES(124,"DJ","ru","name","Джибути")');
            $this->addSql('INSERT INTO "country_translation" VALUES(125,"DM","en","name","Dominica")');
            $this->addSql('INSERT INTO "country_translation" VALUES(126,"DM","ru","name","Остров Доминика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(127,"DO","en","name","Dominican Republic")');
            $this->addSql('INSERT INTO "country_translation" VALUES(128,"DO","ru","name","Доминиканская Республика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(129,"NQ","en","name","Dronning Maud Land")');
            $this->addSql('INSERT INTO "country_translation" VALUES(130,"NQ","ru","name","Земля Королевы Мод")');
            $this->addSql('INSERT INTO "country_translation" VALUES(131,"DD","en","name","East Germany")');
            $this->addSql('INSERT INTO "country_translation" VALUES(132,"DD","ru","name","Германская Демократическая Республика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(133,"EC","en","name","Ecuador")');
            $this->addSql('INSERT INTO "country_translation" VALUES(134,"EC","ru","name","Эквадор")');
            $this->addSql('INSERT INTO "country_translation" VALUES(135,"EG","en","name","Egypt")');
            $this->addSql('INSERT INTO "country_translation" VALUES(136,"EG","ru","name","Египет")');
            $this->addSql('INSERT INTO "country_translation" VALUES(137,"SV","en","name","El Salvador")');
            $this->addSql('INSERT INTO "country_translation" VALUES(138,"SV","ru","name","Сальвадор")');
            $this->addSql('INSERT INTO "country_translation" VALUES(139,"GQ","en","name","Equatorial Guinea")');
            $this->addSql('INSERT INTO "country_translation" VALUES(140,"GQ","ru","name","Экваториальная Гвинея")');
            $this->addSql('INSERT INTO "country_translation" VALUES(141,"ER","en","name","Eritrea")');
            $this->addSql('INSERT INTO "country_translation" VALUES(142,"ER","ru","name","Эритрея")');
            $this->addSql('INSERT INTO "country_translation" VALUES(143,"EE","en","name","Estonia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(144,"EE","ru","name","Эстония")');
            $this->addSql('INSERT INTO "country_translation" VALUES(145,"ET","en","name","Ethiopia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(146,"ET","ru","name","Эфиопия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(147,"FK","en","name","Falkland Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(148,"FK","ru","name","Фолклендские острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(149,"FO","en","name","Faroe Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(150,"FO","ru","name","Фарерские острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(151,"FJ","en","name","Fiji")');
            $this->addSql('INSERT INTO "country_translation" VALUES(152,"FJ","ru","name","Фиджи")');
            $this->addSql('INSERT INTO "country_translation" VALUES(153,"FI","en","name","Finland")');
            $this->addSql('INSERT INTO "country_translation" VALUES(154,"FI","ru","name","Финляндия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(155,"FR","en","name","France")');
            $this->addSql('INSERT INTO "country_translation" VALUES(156,"FR","ru","name","Франция")');
            $this->addSql('INSERT INTO "country_translation" VALUES(157,"GF","en","name","French Guiana")');
            $this->addSql('INSERT INTO "country_translation" VALUES(158,"GF","ru","name","Французская Гвиана")');
            $this->addSql('INSERT INTO "country_translation" VALUES(159,"PF","en","name","French Polynesia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(160,"PF","ru","name","Французская Полинезия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(161,"TF","en","name","French Southern Territories")');
            $this->addSql('INSERT INTO "country_translation" VALUES(162,"TF","ru","name","Французские Южные Территории")');
            $this->addSql('INSERT INTO "country_translation" VALUES(163,"FQ","en","name","French Southern and Antarctic Territories")');
            $this->addSql('INSERT INTO "country_translation" VALUES(164,"FQ","ru","name","Французские Южные и Антарктические территории")');
            $this->addSql('INSERT INTO "country_translation" VALUES(165,"GA","en","name","Gabon")');
            $this->addSql('INSERT INTO "country_translation" VALUES(166,"GA","ru","name","Габон")');
            $this->addSql('INSERT INTO "country_translation" VALUES(167,"GM","en","name","Gambia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(168,"GM","ru","name","Гамбия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(169,"GE","en","name","Georgia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(170,"GE","ru","name","Грузия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(171,"DE","en","name","Germany")');
            $this->addSql('INSERT INTO "country_translation" VALUES(172,"DE","ru","name","Германия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(173,"GH","en","name","Ghana")');
            $this->addSql('INSERT INTO "country_translation" VALUES(174,"GH","ru","name","Гана")');
            $this->addSql('INSERT INTO "country_translation" VALUES(175,"GI","en","name","Gibraltar")');
            $this->addSql('INSERT INTO "country_translation" VALUES(176,"GI","ru","name","Гибралтар")');
            $this->addSql('INSERT INTO "country_translation" VALUES(177,"GR","en","name","Greece")');
            $this->addSql('INSERT INTO "country_translation" VALUES(178,"GR","ru","name","Греция")');
            $this->addSql('INSERT INTO "country_translation" VALUES(179,"GL","en","name","Greenland")');
            $this->addSql('INSERT INTO "country_translation" VALUES(180,"GL","ru","name","Гренландия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(181,"GD","en","name","Grenada")');
            $this->addSql('INSERT INTO "country_translation" VALUES(182,"GD","ru","name","Гренада")');
            $this->addSql('INSERT INTO "country_translation" VALUES(183,"GP","en","name","Guadeloupe")');
            $this->addSql('INSERT INTO "country_translation" VALUES(184,"GP","ru","name","Гваделупа")');
            $this->addSql('INSERT INTO "country_translation" VALUES(185,"GU","en","name","Guam")');
            $this->addSql('INSERT INTO "country_translation" VALUES(186,"GU","ru","name","Гуам")');
            $this->addSql('INSERT INTO "country_translation" VALUES(187,"GT","en","name","Guatemala")');
            $this->addSql('INSERT INTO "country_translation" VALUES(188,"GT","ru","name","Гватемала")');
            $this->addSql('INSERT INTO "country_translation" VALUES(189,"GG","en","name","Guernsey")');
            $this->addSql('INSERT INTO "country_translation" VALUES(190,"GG","ru","name","Гернси")');
            $this->addSql('INSERT INTO "country_translation" VALUES(191,"GN","en","name","Guinea")');
            $this->addSql('INSERT INTO "country_translation" VALUES(192,"GN","ru","name","Гвинея")');
            $this->addSql('INSERT INTO "country_translation" VALUES(193,"GW","en","name","Guinea-Bissau")');
            $this->addSql('INSERT INTO "country_translation" VALUES(194,"GW","ru","name","Гвинея-Биссау")');
            $this->addSql('INSERT INTO "country_translation" VALUES(195,"GY","en","name","Guyana")');
            $this->addSql('INSERT INTO "country_translation" VALUES(196,"GY","ru","name","Гайана")');
            $this->addSql('INSERT INTO "country_translation" VALUES(197,"HT","en","name","Haiti")');
            $this->addSql('INSERT INTO "country_translation" VALUES(198,"HT","ru","name","Гаити")');
            $this->addSql('INSERT INTO "country_translation" VALUES(199,"HM","en","name","Heard Island and McDonald Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(200,"HM","ru","name","Острова Херд и Макдональд")');
            $this->addSql('INSERT INTO "country_translation" VALUES(201,"HN","en","name","Honduras")');
            $this->addSql('INSERT INTO "country_translation" VALUES(202,"HN","ru","name","Гондурас")');
            $this->addSql('INSERT INTO "country_translation" VALUES(203,"HK","en","name","Hong Kong SAR China")');
            $this->addSql('INSERT INTO "country_translation" VALUES(204,"HK","ru","name","Гонконг, Особый Административный Район Китая")');
            $this->addSql('INSERT INTO "country_translation" VALUES(205,"HU","en","name","Hungary")');
            $this->addSql('INSERT INTO "country_translation" VALUES(206,"HU","ru","name","Венгрия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(207,"IS","en","name","Iceland")');
            $this->addSql('INSERT INTO "country_translation" VALUES(208,"IS","ru","name","Исландия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(209,"IN","en","name","India")');
            $this->addSql('INSERT INTO "country_translation" VALUES(210,"IN","ru","name","Индия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(211,"ID","en","name","Indonesia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(212,"ID","ru","name","Индонезия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(213,"IR","en","name","Iran")');
            $this->addSql('INSERT INTO "country_translation" VALUES(214,"IR","ru","name","Иран")');
            $this->addSql('INSERT INTO "country_translation" VALUES(215,"IQ","en","name","Iraq")');
            $this->addSql('INSERT INTO "country_translation" VALUES(216,"IQ","ru","name","Ирак")');
            $this->addSql('INSERT INTO "country_translation" VALUES(217,"IE","en","name","Ireland")');
            $this->addSql('INSERT INTO "country_translation" VALUES(218,"IE","ru","name","Ирландия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(219,"IM","en","name","Isle of Man")');
            $this->addSql('INSERT INTO "country_translation" VALUES(220,"IM","ru","name","Остров Мэн")');
            $this->addSql('INSERT INTO "country_translation" VALUES(221,"IL","en","name","Israel")');
            $this->addSql('INSERT INTO "country_translation" VALUES(222,"IL","ru","name","Израиль")');
            $this->addSql('INSERT INTO "country_translation" VALUES(223,"IT","en","name","Italy")');
            $this->addSql('INSERT INTO "country_translation" VALUES(224,"IT","ru","name","Италия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(225,"JM","en","name","Jamaica")');
            $this->addSql('INSERT INTO "country_translation" VALUES(226,"JM","ru","name","Ямайка")');
            $this->addSql('INSERT INTO "country_translation" VALUES(227,"JP","en","name","Japan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(228,"JP","ru","name","Япония")');
            $this->addSql('INSERT INTO "country_translation" VALUES(229,"JE","en","name","Jersey")');
            $this->addSql('INSERT INTO "country_translation" VALUES(230,"JE","ru","name","Джерси")');
            $this->addSql('INSERT INTO "country_translation" VALUES(231,"JT","en","name","Johnston Island")');
            $this->addSql('INSERT INTO "country_translation" VALUES(232,"JT","ru","name","Джонстон")');
            $this->addSql('INSERT INTO "country_translation" VALUES(233,"JO","en","name","Jordan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(234,"JO","ru","name","Иордания")');
            $this->addSql('INSERT INTO "country_translation" VALUES(235,"KZ","en","name","Kazakhstan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(236,"KZ","ru","name","Казахстан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(237,"KE","en","name","Kenya")');
            $this->addSql('INSERT INTO "country_translation" VALUES(238,"KE","ru","name","Кения")');
            $this->addSql('INSERT INTO "country_translation" VALUES(239,"KI","en","name","Kiribati")');
            $this->addSql('INSERT INTO "country_translation" VALUES(240,"KI","ru","name","Кирибати")');
            $this->addSql('INSERT INTO "country_translation" VALUES(241,"KW","en","name","Kuwait")');
            $this->addSql('INSERT INTO "country_translation" VALUES(242,"KW","ru","name","Кувейт")');
            $this->addSql('INSERT INTO "country_translation" VALUES(243,"KG","en","name","Kyrgyzstan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(244,"KG","ru","name","Кыргызстан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(245,"LA","en","name","Laos")');
            $this->addSql('INSERT INTO "country_translation" VALUES(246,"LA","ru","name","Лаос")');
            $this->addSql('INSERT INTO "country_translation" VALUES(247,"LV","en","name","Latvia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(248,"LV","ru","name","Латвия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(249,"LB","en","name","Lebanon")');
            $this->addSql('INSERT INTO "country_translation" VALUES(250,"LB","ru","name","Ливан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(251,"LS","en","name","Lesotho")');
            $this->addSql('INSERT INTO "country_translation" VALUES(252,"LS","ru","name","Лесото")');
            $this->addSql('INSERT INTO "country_translation" VALUES(253,"LR","en","name","Liberia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(254,"LR","ru","name","Либерия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(255,"LY","en","name","Libya")');
            $this->addSql('INSERT INTO "country_translation" VALUES(256,"LY","ru","name","Ливия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(257,"LI","en","name","Liechtenstein")');
            $this->addSql('INSERT INTO "country_translation" VALUES(258,"LI","ru","name","Лихтенштейн")');
            $this->addSql('INSERT INTO "country_translation" VALUES(259,"LT","en","name","Lithuania")');
            $this->addSql('INSERT INTO "country_translation" VALUES(260,"LT","ru","name","Литва")');
            $this->addSql('INSERT INTO "country_translation" VALUES(261,"LU","en","name","Luxembourg")');
            $this->addSql('INSERT INTO "country_translation" VALUES(262,"LU","ru","name","Люксембург")');
            $this->addSql('INSERT INTO "country_translation" VALUES(263,"MO","en","name","Macau SAR China")');
            $this->addSql('INSERT INTO "country_translation" VALUES(264,"MO","ru","name","Макао (особый административный район КНР)")');
            $this->addSql('INSERT INTO "country_translation" VALUES(265,"MK","en","name","Macedonia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(266,"MK","ru","name","Македония")');
            $this->addSql('INSERT INTO "country_translation" VALUES(267,"MG","en","name","Madagascar")');
            $this->addSql('INSERT INTO "country_translation" VALUES(268,"MG","ru","name","Мадагаскар")');
            $this->addSql('INSERT INTO "country_translation" VALUES(269,"MW","en","name","Malawi")');
            $this->addSql('INSERT INTO "country_translation" VALUES(270,"MW","ru","name","Малави")');
            $this->addSql('INSERT INTO "country_translation" VALUES(271,"MY","en","name","Malaysia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(272,"MY","ru","name","Малайзия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(273,"MV","en","name","Maldives")');
            $this->addSql('INSERT INTO "country_translation" VALUES(274,"MV","ru","name","Мальдивы")');
            $this->addSql('INSERT INTO "country_translation" VALUES(275,"ML","en","name","Mali")');
            $this->addSql('INSERT INTO "country_translation" VALUES(276,"ML","ru","name","Мали")');
            $this->addSql('INSERT INTO "country_translation" VALUES(277,"MT","en","name","Malta")');
            $this->addSql('INSERT INTO "country_translation" VALUES(278,"MT","ru","name","Мальта")');
            $this->addSql('INSERT INTO "country_translation" VALUES(279,"MH","en","name","Marshall Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(280,"MH","ru","name","Маршалловы Острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(281,"MQ","en","name","Martinique")');
            $this->addSql('INSERT INTO "country_translation" VALUES(282,"MQ","ru","name","Мартиник")');
            $this->addSql('INSERT INTO "country_translation" VALUES(283,"MR","en","name","Mauritania")');
            $this->addSql('INSERT INTO "country_translation" VALUES(284,"MR","ru","name","Мавритания")');
            $this->addSql('INSERT INTO "country_translation" VALUES(285,"MU","en","name","Mauritius")');
            $this->addSql('INSERT INTO "country_translation" VALUES(286,"MU","ru","name","Маврикий")');
            $this->addSql('INSERT INTO "country_translation" VALUES(287,"YT","en","name","Mayotte")');
            $this->addSql('INSERT INTO "country_translation" VALUES(288,"YT","ru","name","Майотта")');
            $this->addSql('INSERT INTO "country_translation" VALUES(289,"FX","en","name","Metropolitan France")');
            $this->addSql('INSERT INTO "country_translation" VALUES(290,"FX","ru","name","Метрополия Франции")');
            $this->addSql('INSERT INTO "country_translation" VALUES(291,"MX","en","name","Mexico")');
            $this->addSql('INSERT INTO "country_translation" VALUES(292,"MX","ru","name","Мексика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(293,"FM","en","name","Micronesia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(294,"FM","ru","name","Федеративные Штаты Микронезии")');
            $this->addSql('INSERT INTO "country_translation" VALUES(295,"MI","en","name","Midway Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(296,"MI","ru","name","Мидуэй")');
            $this->addSql('INSERT INTO "country_translation" VALUES(297,"MD","en","name","Moldova")');
            $this->addSql('INSERT INTO "country_translation" VALUES(298,"MD","ru","name","Молдова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(299,"MC","en","name","Monaco")');
            $this->addSql('INSERT INTO "country_translation" VALUES(300,"MC","ru","name","Монако")');
            $this->addSql('INSERT INTO "country_translation" VALUES(301,"MN","en","name","Mongolia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(302,"MN","ru","name","Монголия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(303,"ME","en","name","Montenegro")');
            $this->addSql('INSERT INTO "country_translation" VALUES(304,"ME","ru","name","Черногория")');
            $this->addSql('INSERT INTO "country_translation" VALUES(305,"MS","en","name","Montserrat")');
            $this->addSql('INSERT INTO "country_translation" VALUES(306,"MS","ru","name","Монсеррат")');
            $this->addSql('INSERT INTO "country_translation" VALUES(307,"MA","en","name","Morocco")');
            $this->addSql('INSERT INTO "country_translation" VALUES(308,"MA","ru","name","Марокко")');
            $this->addSql('INSERT INTO "country_translation" VALUES(309,"MZ","en","name","Mozambique")');
            $this->addSql('INSERT INTO "country_translation" VALUES(310,"MZ","ru","name","Мозамбик")');
            $this->addSql('INSERT INTO "country_translation" VALUES(311,"MM","en","name","Myanmar (Burma)")');
            $this->addSql('INSERT INTO "country_translation" VALUES(312,"MM","ru","name","Мьянма")');
            $this->addSql('INSERT INTO "country_translation" VALUES(313,"NA","en","name","Namibia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(314,"NA","ru","name","Намибия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(315,"NR","en","name","Nauru")');
            $this->addSql('INSERT INTO "country_translation" VALUES(316,"NR","ru","name","Науру")');
            $this->addSql('INSERT INTO "country_translation" VALUES(317,"NP","en","name","Nepal")');
            $this->addSql('INSERT INTO "country_translation" VALUES(318,"NP","ru","name","Непал")');
            $this->addSql('INSERT INTO "country_translation" VALUES(319,"NL","en","name","Netherlands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(320,"NL","ru","name","Нидерланды")');
            $this->addSql('INSERT INTO "country_translation" VALUES(321,"AN","en","name","Netherlands Antilles")');
            $this->addSql('INSERT INTO "country_translation" VALUES(322,"AN","ru","name","Нидерландские Антильские острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(323,"NT","en","name","Neutral Zone")');
            $this->addSql('INSERT INTO "country_translation" VALUES(324,"NT","ru","name","Нейтральная зона (саудовско-иракская)")');
            $this->addSql('INSERT INTO "country_translation" VALUES(325,"NC","en","name","New Caledonia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(326,"NC","ru","name","Новая Каледония")');
            $this->addSql('INSERT INTO "country_translation" VALUES(327,"NZ","en","name","New Zealand")');
            $this->addSql('INSERT INTO "country_translation" VALUES(328,"NZ","ru","name","Новая Зеландия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(329,"NI","en","name","Nicaragua")');
            $this->addSql('INSERT INTO "country_translation" VALUES(330,"NI","ru","name","Никарагуа")');
            $this->addSql('INSERT INTO "country_translation" VALUES(331,"NE","en","name","Niger")');
            $this->addSql('INSERT INTO "country_translation" VALUES(332,"NE","ru","name","Нигер")');
            $this->addSql('INSERT INTO "country_translation" VALUES(333,"NG","en","name","Nigeria")');
            $this->addSql('INSERT INTO "country_translation" VALUES(334,"NG","ru","name","Нигерия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(335,"NU","en","name","Niue")');
            $this->addSql('INSERT INTO "country_translation" VALUES(336,"NU","ru","name","Ниуе")');
            $this->addSql('INSERT INTO "country_translation" VALUES(337,"NF","en","name","Norfolk Island")');
            $this->addSql('INSERT INTO "country_translation" VALUES(338,"NF","ru","name","Остров Норфолк")');
            $this->addSql('INSERT INTO "country_translation" VALUES(339,"KP","en","name","North Korea")');
            $this->addSql('INSERT INTO "country_translation" VALUES(340,"KP","ru","name","Корейская Народно-Демократическая Республика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(341,"VD","en","name","North Vietnam")');
            $this->addSql('INSERT INTO "country_translation" VALUES(342,"VD","ru","name","Демократическая Республика Вьетнам")');
            $this->addSql('INSERT INTO "country_translation" VALUES(343,"MP","en","name","Northern Mariana Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(344,"MP","ru","name","Северные Марианские Острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(345,"NO","en","name","Norway")');
            $this->addSql('INSERT INTO "country_translation" VALUES(346,"NO","ru","name","Норвегия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(347,"OM","en","name","Oman")');
            $this->addSql('INSERT INTO "country_translation" VALUES(348,"OM","ru","name","Оман")');
            $this->addSql('INSERT INTO "country_translation" VALUES(349,"PC","en","name","Pacific Islands Trust Territory")');
            $this->addSql('INSERT INTO "country_translation" VALUES(350,"PC","ru","name","Подопечная территория Тихоокеанские острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(351,"PK","en","name","Pakistan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(352,"PK","ru","name","Пакистан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(353,"PW","en","name","Palau")');
            $this->addSql('INSERT INTO "country_translation" VALUES(354,"PW","ru","name","Палау")');
            $this->addSql('INSERT INTO "country_translation" VALUES(355,"PS","en","name","Palestinian Territories")');
            $this->addSql('INSERT INTO "country_translation" VALUES(356,"PS","ru","name","Палестинская автономия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(357,"PA","en","name","Panama")');
            $this->addSql('INSERT INTO "country_translation" VALUES(358,"PA","ru","name","Панама")');
            $this->addSql('INSERT INTO "country_translation" VALUES(359,"PZ","en","name","Panama Canal Zone")');
            $this->addSql('INSERT INTO "country_translation" VALUES(360,"PZ","ru","name","Зона Панамского канала")');
            $this->addSql('INSERT INTO "country_translation" VALUES(361,"PG","en","name","Papua New Guinea")');
            $this->addSql('INSERT INTO "country_translation" VALUES(362,"PG","ru","name","Папуа-Новая Гвинея")');
            $this->addSql('INSERT INTO "country_translation" VALUES(363,"PY","en","name","Paraguay")');
            $this->addSql('INSERT INTO "country_translation" VALUES(364,"PY","ru","name","Парагвай")');
            $this->addSql('INSERT INTO "country_translation" VALUES(365,"YD","en","name","People`s Democratic Republic of Yemen")');
            $this->addSql('INSERT INTO "country_translation" VALUES(366,"YD","ru","name","Народная Демократическая Республика Йемен")');
            $this->addSql('INSERT INTO "country_translation" VALUES(367,"PE","en","name","Peru")');
            $this->addSql('INSERT INTO "country_translation" VALUES(368,"PE","ru","name","Перу")');
            $this->addSql('INSERT INTO "country_translation" VALUES(369,"PH","en","name","Philippines")');
            $this->addSql('INSERT INTO "country_translation" VALUES(370,"PH","ru","name","Филиппины")');
            $this->addSql('INSERT INTO "country_translation" VALUES(371,"PN","en","name","Pitcairn Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(372,"PN","ru","name","Питкерн")');
            $this->addSql('INSERT INTO "country_translation" VALUES(373,"PL","en","name","Poland")');
            $this->addSql('INSERT INTO "country_translation" VALUES(374,"PL","ru","name","Польша")');
            $this->addSql('INSERT INTO "country_translation" VALUES(375,"PT","en","name","Portugal")');
            $this->addSql('INSERT INTO "country_translation" VALUES(376,"PT","ru","name","Португалия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(377,"PR","en","name","Puerto Rico")');
            $this->addSql('INSERT INTO "country_translation" VALUES(378,"PR","ru","name","Пуэрто-Рико")');
            $this->addSql('INSERT INTO "country_translation" VALUES(379,"QA","en","name","Qatar")');
            $this->addSql('INSERT INTO "country_translation" VALUES(380,"QA","ru","name","Катар")');
            $this->addSql('INSERT INTO "country_translation" VALUES(381,"RO","en","name","Romania")');
            $this->addSql('INSERT INTO "country_translation" VALUES(382,"RO","ru","name","Румыния")');
            $this->addSql('INSERT INTO "country_translation" VALUES(383,"RU","en","name","Russia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(384,"RU","ru","name","Россия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(385,"RW","en","name","Rwanda")');
            $this->addSql('INSERT INTO "country_translation" VALUES(386,"RW","ru","name","Руанда")');
            $this->addSql('INSERT INTO "country_translation" VALUES(387,"RE","en","name","Réunion")');
            $this->addSql('INSERT INTO "country_translation" VALUES(388,"RE","ru","name","Реюньон")');
            $this->addSql('INSERT INTO "country_translation" VALUES(389,"BL","en","name","Saint Barthélemy")');
            $this->addSql('INSERT INTO "country_translation" VALUES(390,"BL","ru","name","Остров Святого Бартоломея")');
            $this->addSql('INSERT INTO "country_translation" VALUES(391,"SH","en","name","Saint Helena")');
            $this->addSql('INSERT INTO "country_translation" VALUES(392,"SH","ru","name","Остров Святой Елены")');
            $this->addSql('INSERT INTO "country_translation" VALUES(393,"KN","en","name","Saint Kitts and Nevis")');
            $this->addSql('INSERT INTO "country_translation" VALUES(394,"KN","ru","name","Сент-Киттс и Невис")');
            $this->addSql('INSERT INTO "country_translation" VALUES(395,"LC","en","name","Saint Lucia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(396,"LC","ru","name","Сент-Люсия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(397,"MF","en","name","Saint Martin")');
            $this->addSql('INSERT INTO "country_translation" VALUES(398,"MF","ru","name","Остров Святого Мартина")');
            $this->addSql('INSERT INTO "country_translation" VALUES(399,"PM","en","name","Saint Pierre and Miquelon")');
            $this->addSql('INSERT INTO "country_translation" VALUES(400,"PM","ru","name","Сен-Пьер и Микелон")');
            $this->addSql('INSERT INTO "country_translation" VALUES(401,"VC","en","name","Saint Vincent and the Grenadines")');
            $this->addSql('INSERT INTO "country_translation" VALUES(402,"VC","ru","name","Сент-Винсент и Гренадины")');
            $this->addSql('INSERT INTO "country_translation" VALUES(403,"WS","en","name","Samoa")');
            $this->addSql('INSERT INTO "country_translation" VALUES(404,"WS","ru","name","Самоа")');
            $this->addSql('INSERT INTO "country_translation" VALUES(405,"SM","en","name","San Marino")');
            $this->addSql('INSERT INTO "country_translation" VALUES(406,"SM","ru","name","Сан-Марино")');
            $this->addSql('INSERT INTO "country_translation" VALUES(407,"SA","en","name","Saudi Arabia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(408,"SA","ru","name","Саудовская Аравия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(409,"SN","en","name","Senegal")');
            $this->addSql('INSERT INTO "country_translation" VALUES(410,"SN","ru","name","Сенегал")');
            $this->addSql('INSERT INTO "country_translation" VALUES(411,"RS","en","name","Serbia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(412,"RS","ru","name","Сербия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(413,"CS","en","name","Serbia and Montenegro")');
            $this->addSql('INSERT INTO "country_translation" VALUES(414,"CS","ru","name","Сербия и Черногория")');
            $this->addSql('INSERT INTO "country_translation" VALUES(415,"SC","en","name","Seychelles")');
            $this->addSql('INSERT INTO "country_translation" VALUES(416,"SC","ru","name","Сейшельские Острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(417,"SL","en","name","Sierra Leone")');
            $this->addSql('INSERT INTO "country_translation" VALUES(418,"SL","ru","name","Сьерра-Леоне")');
            $this->addSql('INSERT INTO "country_translation" VALUES(419,"SG","en","name","Singapore")');
            $this->addSql('INSERT INTO "country_translation" VALUES(420,"SG","ru","name","Сингапур")');
            $this->addSql('INSERT INTO "country_translation" VALUES(421,"SK","en","name","Slovakia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(422,"SK","ru","name","Словакия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(423,"SI","en","name","Slovenia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(424,"SI","ru","name","Словения")');
            $this->addSql('INSERT INTO "country_translation" VALUES(425,"SB","en","name","Solomon Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(426,"SB","ru","name","Соломоновы Острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(427,"SO","en","name","Somalia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(428,"SO","ru","name","Сомали")');
            $this->addSql('INSERT INTO "country_translation" VALUES(429,"ZA","en","name","South Africa")');
            $this->addSql('INSERT INTO "country_translation" VALUES(430,"ZA","ru","name","Южная Африка")');
            $this->addSql('INSERT INTO "country_translation" VALUES(431,"GS","en","name","South Georgia and the South Sandwich Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(432,"GS","ru","name","Южная Джорджия и Южные Сандвичевы Острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(433,"KR","en","name","South Korea")');
            $this->addSql('INSERT INTO "country_translation" VALUES(434,"KR","ru","name","Республика Корея")');
            $this->addSql('INSERT INTO "country_translation" VALUES(435,"ES","en","name","Spain")');
            $this->addSql('INSERT INTO "country_translation" VALUES(436,"ES","ru","name","Испания")');
            $this->addSql('INSERT INTO "country_translation" VALUES(437,"LK","en","name","Sri Lanka")');
            $this->addSql('INSERT INTO "country_translation" VALUES(438,"LK","ru","name","Шри-Ланка")');
            $this->addSql('INSERT INTO "country_translation" VALUES(439,"SD","en","name","Sudan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(440,"SD","ru","name","Судан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(441,"SR","en","name","Suriname")');
            $this->addSql('INSERT INTO "country_translation" VALUES(442,"SR","ru","name","Суринам")');
            $this->addSql('INSERT INTO "country_translation" VALUES(443,"SJ","en","name","Svalbard and Jan Mayen")');
            $this->addSql('INSERT INTO "country_translation" VALUES(444,"SJ","ru","name","Свальбард и Ян-Майен")');
            $this->addSql('INSERT INTO "country_translation" VALUES(445,"SZ","en","name","Swaziland")');
            $this->addSql('INSERT INTO "country_translation" VALUES(446,"SZ","ru","name","Свазиленд")');
            $this->addSql('INSERT INTO "country_translation" VALUES(447,"SE","en","name","Sweden")');
            $this->addSql('INSERT INTO "country_translation" VALUES(448,"SE","ru","name","Швеция")');
            $this->addSql('INSERT INTO "country_translation" VALUES(449,"CH","en","name","Switzerland")');
            $this->addSql('INSERT INTO "country_translation" VALUES(450,"CH","ru","name","Швейцария")');
            $this->addSql('INSERT INTO "country_translation" VALUES(451,"SY","en","name","Syria")');
            $this->addSql('INSERT INTO "country_translation" VALUES(452,"SY","ru","name","Сирийская Арабская Республика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(453,"ST","en","name","São Tomé and Príncipe")');
            $this->addSql('INSERT INTO "country_translation" VALUES(454,"ST","ru","name","Сан-Томе и Принсипи")');
            $this->addSql('INSERT INTO "country_translation" VALUES(455,"TW","en","name","Taiwan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(456,"TW","ru","name","Тайвань")');
            $this->addSql('INSERT INTO "country_translation" VALUES(457,"TJ","en","name","Tajikistan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(458,"TJ","ru","name","Таджикистан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(459,"TZ","en","name","Tanzania")');
            $this->addSql('INSERT INTO "country_translation" VALUES(460,"TZ","ru","name","Танзания")');
            $this->addSql('INSERT INTO "country_translation" VALUES(461,"TH","en","name","Thailand")');
            $this->addSql('INSERT INTO "country_translation" VALUES(462,"TH","ru","name","Таиланд")');
            $this->addSql('INSERT INTO "country_translation" VALUES(463,"TL","en","name","Timor-Leste")');
            $this->addSql('INSERT INTO "country_translation" VALUES(464,"TL","ru","name","Восточный Тимор")');
            $this->addSql('INSERT INTO "country_translation" VALUES(465,"TG","en","name","Togo")');
            $this->addSql('INSERT INTO "country_translation" VALUES(466,"TG","ru","name","Того")');
            $this->addSql('INSERT INTO "country_translation" VALUES(467,"TK","en","name","Tokelau")');
            $this->addSql('INSERT INTO "country_translation" VALUES(468,"TK","ru","name","Токелау")');
            $this->addSql('INSERT INTO "country_translation" VALUES(469,"TO","en","name","Tonga")');
            $this->addSql('INSERT INTO "country_translation" VALUES(470,"TO","ru","name","Тонга")');
            $this->addSql('INSERT INTO "country_translation" VALUES(471,"TT","en","name","Trinidad and Tobago")');
            $this->addSql('INSERT INTO "country_translation" VALUES(472,"TT","ru","name","Тринидад и Тобаго")');
            $this->addSql('INSERT INTO "country_translation" VALUES(473,"TN","en","name","Tunisia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(474,"TN","ru","name","Тунис")');
            $this->addSql('INSERT INTO "country_translation" VALUES(475,"TR","en","name","Turkey")');
            $this->addSql('INSERT INTO "country_translation" VALUES(476,"TR","ru","name","Турция")');
            $this->addSql('INSERT INTO "country_translation" VALUES(477,"TM","en","name","Turkmenistan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(478,"TM","ru","name","Туркменистан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(479,"TC","en","name","Turks and Caicos Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(480,"TC","ru","name","Острова Тёркс и Кайкос")');
            $this->addSql('INSERT INTO "country_translation" VALUES(481,"TV","en","name","Tuvalu")');
            $this->addSql('INSERT INTO "country_translation" VALUES(482,"TV","ru","name","Тувалу")');
            $this->addSql('INSERT INTO "country_translation" VALUES(483,"UM","en","name","U.S. Minor Outlying Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(484,"UM","ru","name","Внешние малые острова (США)")');
            $this->addSql('INSERT INTO "country_translation" VALUES(485,"PU","en","name","U.S. Miscellaneous Pacific Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(486,"PU","ru","name","Малые отдаленные острова Соединенных Штатов")');
            $this->addSql('INSERT INTO "country_translation" VALUES(487,"VI","en","name","U.S. Virgin Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(488,"VI","ru","name","Американские Виргинские острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(489,"UG","en","name","Uganda")');
            $this->addSql('INSERT INTO "country_translation" VALUES(490,"UG","ru","name","Уганда")');
            $this->addSql('INSERT INTO "country_translation" VALUES(491,"UA","en","name","Ukraine")');
            $this->addSql('INSERT INTO "country_translation" VALUES(492,"UA","ru","name","Украина")');
            $this->addSql('INSERT INTO "country_translation" VALUES(493,"SU","en","name","Union of Soviet Socialist Republics")');
            $this->addSql('INSERT INTO "country_translation" VALUES(494,"SU","ru","name","СССР")');
            $this->addSql('INSERT INTO "country_translation" VALUES(495,"AE","en","name","United Arab Emirates")');
            $this->addSql('INSERT INTO "country_translation" VALUES(496,"AE","ru","name","Объединенные Арабские Эмираты")');
            $this->addSql('INSERT INTO "country_translation" VALUES(497,"GB","en","name","United Kingdom")');
            $this->addSql('INSERT INTO "country_translation" VALUES(498,"GB","ru","name","Великобритания")');
            $this->addSql('INSERT INTO "country_translation" VALUES(499,"US","en","name","United States")');
            $this->addSql('INSERT INTO "country_translation" VALUES(500,"US","ru","name","США")');
            $this->addSql('INSERT INTO "country_translation" VALUES(501,"ZZ","en","name","Unknown or Invalid Region")');
            $this->addSql('INSERT INTO "country_translation" VALUES(502,"ZZ","ru","name","Неизвестный или недействительный регион")');
            $this->addSql('INSERT INTO "country_translation" VALUES(503,"UY","en","name","Uruguay")');
            $this->addSql('INSERT INTO "country_translation" VALUES(504,"UY","ru","name","Уругвай")');
            $this->addSql('INSERT INTO "country_translation" VALUES(505,"UZ","en","name","Uzbekistan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(506,"UZ","ru","name","Узбекистан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(507,"VU","en","name","Vanuatu")');
            $this->addSql('INSERT INTO "country_translation" VALUES(508,"VU","ru","name","Вануату")');
            $this->addSql('INSERT INTO "country_translation" VALUES(509,"VA","en","name","Vatican City")');
            $this->addSql('INSERT INTO "country_translation" VALUES(510,"VA","ru","name","Ватикан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(511,"VE","en","name","Venezuela")');
            $this->addSql('INSERT INTO "country_translation" VALUES(512,"VE","ru","name","Венесуэла")');
            $this->addSql('INSERT INTO "country_translation" VALUES(513,"VN","en","name","Vietnam")');
            $this->addSql('INSERT INTO "country_translation" VALUES(514,"VN","ru","name","Вьетнам")');
            $this->addSql('INSERT INTO "country_translation" VALUES(515,"WK","en","name","Wake Island")');
            $this->addSql('INSERT INTO "country_translation" VALUES(516,"WK","ru","name","Уэйк")');
            $this->addSql('INSERT INTO "country_translation" VALUES(517,"WF","en","name","Wallis and Futuna")');
            $this->addSql('INSERT INTO "country_translation" VALUES(518,"WF","ru","name","Уоллис и Футуна")');
            $this->addSql('INSERT INTO "country_translation" VALUES(519,"EH","en","name","Western Sahara")');
            $this->addSql('INSERT INTO "country_translation" VALUES(520,"EH","ru","name","Западная Сахара")');
            $this->addSql('INSERT INTO "country_translation" VALUES(521,"YE","en","name","Yemen")');
            $this->addSql('INSERT INTO "country_translation" VALUES(522,"YE","ru","name","Йемен")');
            $this->addSql('INSERT INTO "country_translation" VALUES(523,"ZM","en","name","Zambia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(524,"ZM","ru","name","Замбия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(525,"ZW","en","name","Zimbabwe")');
            $this->addSql('INSERT INTO "country_translation" VALUES(526,"ZW","ru","name","Зимбабве")');
            $this->addSql('INSERT INTO "country_translation" VALUES(527,"AX","en","name","Åland Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(528,"AX","ru","name","Аландские острова")');
    }
}
