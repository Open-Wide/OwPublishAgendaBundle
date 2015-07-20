<?php

class OWPAgenda_003_AgendaEvent
{

    public function up()
    {
        $migration = new OWMigrationContentClass( );
        $migration->startMigrationOn( 'agenda_event' );
        $migration->createIfNotExists();

        $migration->contentobject_name = '<short_name|name>';
        $migration->is_container = TRUE;
        $migration->name = array(
            'eng-GB' => 'Event',
            'fre-FR' => 'Événement',
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

        $migration->addAttribute( 'subtitle', array(
            'name' => array(
                'eng-GB' => 'Subtitle',
                'fre-FR' => 'Sous-titre',
                'always-available' => 'eng-GB'
            )
        ) );

        $migration->addAttribute( 'image', array(
            'data_type_string' => 'ezobjectrelation',
            'name' => array(
                'eng-GB' => 'Image',
                'fre-FR' => 'Visuel',
                'always-available' => 'eng-GB'
            ),
            'selection_method' => 'Browse',
            'default_selection_node' => 'media',
            'fuzzy_match' => FALSE
        ) );
        $migration->addAttribute( 'description', array(
            'data_type_string' => 'ezxmltext',
            'is_required' => TRUE,
            'name' => array(
                'eng-GB' => 'Description',
                'fre-FR' => 'Description',
                'always-available' => 'eng-GB'
            )
        ) );
        $migration->addAttribute( 'publish_start', array(
            'data_type_string' => 'ezdatetime',
            'is_required' => TRUE,
            'can_translate' => FALSE,
            'name' => array(
                'eng-GB' => 'Publish date',
                'fre-FR' => 'Date de publication',
                'always-available' => 'eng-GB'
            ),
            'set_with_current_date' => TRUE
        ) );
        $migration->addAttribute( 'publish_end', array(
            'data_type_string' => 'ezdatetime',
            'can_translate' => FALSE,
            'name' => array(
                'eng-GB' => 'Unpublish date',
                'fre-FR' => 'Date de dépublication',
                'always-available' => 'eng-GB'
            )
        ) );

        $migration->addToContentClassGroup( 'Agenda' );
        $migration->end();
    }

    public function down()
    {
        $migration = new OWMigrationContentClass( );
        $migration->startMigrationOn( 'agenda_event' );
        $migration->removeClass();
    }

}

?>