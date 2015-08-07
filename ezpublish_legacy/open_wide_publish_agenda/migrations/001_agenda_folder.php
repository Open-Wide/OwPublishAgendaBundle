<?php

class OWPAgenda_001_AgendaFolder
{

    public function up()
    {
        $migration = new OWMigrationContentClass( );
        $migration->startMigrationOn( 'agenda_folder' );
        $migration->createIfNotExists();

        $migration->contentobject_name = '<short_name|name>';
        $migration->is_container = TRUE;
        $migration->name = array(
            'eng-GB' => 'Agendas folder',
            'fre-FR' => 'Dossier des agendas',
            'always-available' => 'eng-GB'
        );

        $migration->addAttribute( 'name', array(
            'is_required' => TRUE,
            'name' => array(
                'eng-GB' => 'Name',
                'fre-FR' => 'Nom',
                'always-available' => 'eng-GB'
            )
        ) );

        $migration->addAttribute( 'short_name', array(
            'name' => array(
                'eng-GB' => 'Short name',
                'fre-FR' => 'Nom court',
                'always-available' => 'eng-GB'
            ),
            'max_length' => 100
        ) );

        $migration->addToContentClassGroup( 'Agenda' );
        $migration->end();
    }

    public function down()
    {
        $migration = new OWMigrationContentClass( );
        $migration->startMigrationOn( 'agenda_folder' );
        $migration->removeClass();
    }

}