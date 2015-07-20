==================================================
OWPublishAgendaBundle for eZ Publish documentation
==================================================

.. image:: https://github.com/Open-Wide/OWPublishAgendaBundle/raw/master/doc/images/Open-Wide_logo.png

:Extension: OW Publish Agenda Bundle v1.1
:Requires: eZ Publish 5.3.x
:Author: Open Wide http://www.openwide.fr

Presentation
============

This extension provides a complete system to create and show events in a calendar view in front office via [FullCalendar](http://fullcalendar.io/).

LICENCE
-------
This eZ Publish extension is provided *as is*, in GPL v3 (see LICENCE).

Installation
============

1. Add ``PublishAgendaBundle`` in your project

    * Via composer
    
        Add the bundle in your ``composer.json``
        
        .. code-block:: json
        
            {
              "require": {
                "open-wide/ezpublish-agenda-bundle": "dev-master"
              }
            }
    
    * Via a submodule
    
        .. code-block:: bash
        
            mkdir -p src/OpenWide/Publish
            git submodule add https://github.com/Open-Wide/OwPublishAgendaBundle.git src/OpenWide/Publish/AgendaBundle

2. Enable the Bundle in your ``EzPublishKernel.php`` file:

    .. code-block:: php
    
        <?php
        // ezpublish/EzPublishKernel.php
    
        public function registerBundles()
        {
          $bundles = array(
            // ...
            new OpenWide\Publish\AgendaBundle\OpenWidePublishAgendaBundle(),
          );
        }


3. Create the following classes using the content package in ``Package`` directory or using [OWMigration](https://github.com/Open-Wide/OWMigration):


    * In the class group ``Agenda``
        * ``agenda_folder`` : Agendas folder
        * ``agenda`` : Agenda
        * ``agenda_event`` : Event
        * ``agenda_schedule`` : Event schedule

4. Add your ``agenda_folder`` LocationId in ``ezpublish/config/config.yml``:

    .. code-block:: yaml
            
            parameters:
                # LocationId of Agenda
                ezsettings.default.open_wide_publish_agenda.agenda_folder.location_id: ...
                # Nb of element per page
                ezsettings.default.open_wide_publish_agenda.paginate.max_per_page: 10

5. Create contents on back-office with the following structure:


    * agenda_folder
        * agenda
            * agenda_event
                * agenda_schedule
                * agenda_schedule
                * agenda_schedule
            * agenda_event
                * agenda_schedule
        * agenda
            * agenda_event
                * agenda_schedule

6. Run the legacy bundle install script manually:

    .. code-block:: bash
    
        $ php ezpublish/console ezpublish:legacybundles:install_extensions


    By default, it will create an absolute symlink, but options exist to use a hard copy (``--copy``) or a relative link (``--relative``).


7. Add this bundle on your assetic bundles array in ``ezpublish/config/config.yml``:


    .. code-block:: yaml
    
        # Assetic Configuration
        assetic:
            bundles:        [ OtherBundle, OpenWidePublishAgendaBundle ]
            ...
            assets:
                glyphicons-halflings-regular-eot:
                    inputs: '@OpenWidePublishAgendaBundle/Resources/public/fonts/glyphicons-halflings-regular.eot'
                    output: 'fonts/glyphicons-halflings-regular.otf'
                glyphicons-halflings-regular-ttf:
                    inputs: '@OpenWidePublishAgendaBundle/Resources/public/fonts/glyphicons-halflings-regular.ttf'
                    output: 'fonts/glyphicons-halflings-regular.ttf'
                glyphicons-halflings-regular-woff2:
                    inputs: '@OpenWidePublishAgendaBundle/Resources/public/fonts/glyphicons-halflings-regular.woff2'
                    output: 'fonts/glyphicons-halflings-regular.woff2'
                glyphicons-halflings-regular-svg:
                    inputs: '@OpenWidePublishAgendaBundle/Resources/public/fonts/glyphicons-halflings-regular.svg'
                    output: 'fonts/glyphicons-halflings-regular.svg'
                glyphicons-halflings-regular-woff:
                    inputs: '@OpenWidePublishAgendaBundle/Resources/public/fonts/glyphicons-halflings-regular.woff'
                    output: 'fonts/glyphicons-halflings-regular.woff'


9. Import ezpublish.yml configuration in ``ezpublish/config/ezpublish.yml``:


    .. code-block:: yaml
    
        imports:
            - {resource: @OpenWidePublishAgendaBundle/Resources/config/ezpublish.yml}


10. Import routing.yml configuration in ``ezpublish/config/routing.yml``:


    .. code-block:: yaml
    
        agenda:
            resource: "@OpenWidePublishAgendaBundle/Resources/config/routing.yml"


11. Regenerate the Assetic with the following command:


    .. code-block:: sh
    
        $ php ezpublish/console assetic:dump web

Usage
=====

Front Office Calendar View
--------------------------
.. image:: https://github.com/Open-Wide/OWPublishAgendaBundle/raw/master/doc/images/calendar.png


Front Office Calendar Mini View
-------------------------------
.. image:: https://github.com/Open-Wide/OWPublishAgendaBundle/raw/master/doc/images/calendar_mini.png


Other
=====

FullCalendar documentation: http://fullcalendar.io/docs/
