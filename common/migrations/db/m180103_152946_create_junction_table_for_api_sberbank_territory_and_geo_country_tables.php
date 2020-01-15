<?php

use yii\db\Migration;

/**
 * Handles the creation of table `api_sberbank_territory2dict`.
 * Has foreign keys to the tables:
 *
 * - `api_sberbank_territory`
 * - `geo_country`
 */
class m180103_152946_create_junction_table_for_api_sberbank_territory_and_geo_country_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('api_sberbank_territory2dict', [
            'territory_id' => $this->integer(),
            'internal_id' => $this->integer(),
            'PRIMARY KEY(territory_id, internal_id)',
        ], "ENGINE=InnoDB");

        // creates index for column `territory_id`
        $this->createIndex(
            'idx-api_sberbank_territory2dict-territory_id',
            'api_sberbank_territory2dict',
            'territory_id'
        );

        // add foreign key for table `api_sberbank_territory`
        $this->addForeignKey(
            'fk-api_sberbank_territory2dict-territory_id',
            'api_sberbank_territory2dict',
            'territory_id',
            'api_sberbank_territory',
            'id',
            'CASCADE'
        );

        // creates index for column `internal_id`
        $this->createIndex(
            'idx-api_sberbank_territory2dict-internal_id',
            'api_sberbank_territory2dict',
            'internal_id'
        );

        // add foreign key for table `geo_country`
        $this->addForeignKey(
            'fk-api_sberbank_territory2dict-internal_id',
            'api_sberbank_territory2dict',
            'internal_id',
            'geo_country',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `api_sberbank_territory`
        $this->dropForeignKey(
            'fk-api_sberbank_territory2dict-territory_id',
            'api_sberbank_territory2dict'
        );

        // drops index for column `territory_id`
        $this->dropIndex(
            'idx-api_sberbank_territory2dict-territory_id',
            'api_sberbank_territory2dict'
        );

        // drops foreign key for table `geo_country`
        $this->dropForeignKey(
            'fk-api_sberbank_territory2dict-internal_id',
            'api_sberbank_territory2dict'
        );

        // drops index for column `internal_id`
        $this->dropIndex(
            'idx-api_sberbank_territory2dict-internal_id',
            'api_sberbank_territory2dict'
        );

        $this->dropTable('api_sberbank_territory2dict');
    }
}
