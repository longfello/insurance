<?php

use yii\db\Migration;

/**
 * Handles the creation of table `api_sberbank_territory2program`.
 * Has foreign keys to the tables:
 *
 * - `api_sberbank_territory`
 * - `api_sberbank_program`
 */
class m180103_154408_create_junction_table_for_api_sberbank_territory_and_api_sberbank_programm_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('api_sberbank_territory2program', [
            'territory_id' => $this->integer(),
            'program_id' => $this->integer(),
            'PRIMARY KEY(territory_id, program_id)',
        ], "ENGINE=InnoDB");

        // creates index for column `territory_id`
        $this->createIndex(
            'idx-api_sberbank_territory2program-territory_id',
            'api_sberbank_territory2program',
            'territory_id'
        );

        // add foreign key for table `api_sberbank_territory`
        $this->addForeignKey(
            'fk-api_sberbank_territory2program-territory_id',
            'api_sberbank_territory2program',
            'territory_id',
            'api_sberbank_territory',
            'id',
            'CASCADE'
        );

        // creates index for column `program_id`
        $this->createIndex(
            'idx-api_sberbank_territory2program-program_id',
            'api_sberbank_territory2program',
            'program_id'
        );

        // add foreign key for table `api_sberbank_program`
        $this->addForeignKey(
            'fk-api_sberbank_territory2program-program_id',
            'api_sberbank_territory2program',
            'program_id',
            'api_sberbank_program',
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
            'fk-api_sberbank_territory2program-territory_id',
            'api_sberbank_territory2program'
        );

        // drops index for column `territory_id`
        $this->dropIndex(
            'idx-api_sberbank_territory2program-territory_id',
            'api_sberbank_territory2program'
        );

        // drops foreign key for table `api_sberbank_program`
        $this->dropForeignKey(
            'fk-api_sberbank_territory2program-program_id',
            'api_sberbank_territory2program'
        );

        // drops index for column `program_id`
        $this->dropIndex(
            'idx-api_sberbank_territory2program-program_id',
            'api_sberbank_territory2program'
        );

        $this->dropTable('api_sberbank_territory2program');
    }
}
