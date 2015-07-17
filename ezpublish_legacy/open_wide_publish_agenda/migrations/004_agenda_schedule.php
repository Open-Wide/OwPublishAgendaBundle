<?php

class OWPAgenda_004_AgendaSchedule
{

    public function up()
    {
        $migration = new OWMigrationContentClass( );
        $migration->startMigrationOn( 'agenda_schedule' );
        $migration->createIfNotExists();

        $migration->contentobject_name = '<date_start> - <date_end>';
        $migration->name = array(
            'eng-GB' => 'Event schedule',
            'fre-FR' => 'Programmation d\'événement',
            'always-available' => 'eng-GB'
        );

        $migration->addAttribute( 'date_start', array(
            'data_type_string' => 'ezdate',
            'is_required' => TRUE,
            'name' => array(
                'eng-GB' => 'Start date',
                'fre-FR' => 'Date de début',
                'always-available' => 'eng-GB'
            )
        ) );
        $migration->addAttribute( 'date_end', array(
            'data_type_string' => 'ezdate',
            'name' => array(
                'eng-GB' => 'End date',
                'fre-FR' => 'Date de fin',
                'always-available' => 'eng-GB'
            )
        ) );
        $migration->addAttribute( 'hour_start', array(
            'data_type_string' => 'eztime',
            'is_required' => TRUE,
            'is_searchable' => FALSE,
            'name' => array(
                'eng-GB' => 'Start hour',
                'fre-FR' => 'Horaire de début',
                'always-available' => 'eng-GB'
            )
        ) );
        $migration->addAttribute( 'hour_end', array(
            'data_type_string' => 'eztime',
            'is_required' => TRUE,
            'is_searchable' => FALSE,
            'name' => array(
                'eng-GB' => 'End hour',
                'fre-FR' => 'Horaire de fin',
                'always-available' => 'eng-GB'
            )
        ) );
        $migration->addAttribute( 'duration', array(
            'data_type_string' => 'eztime',
            'is_searchable' => FALSE,
            'name' => array(
                'eng-GB' => 'Duration',
                'fre-FR' => 'Durée',
                'always-available' => 'eng-GB'
            )
        ) );

        $migration->addToContentClassGroup( 'Agenda' );
        $migration->end();
    }

    public function down()
    {
        $migration = new OWMigrationContentClass( );
        $migration->startMigrationOn( 'agenda_schedule' );
        $migration->removeClass();
    }

}

?>