===========================================
OWAgendaBundle for eZ Publish documentation
===========================================

.. image:: https://github.com/Open-Wide/OWAgendaBundle/raw/master/doc/images/Open-Wide_logo.png

:Extension: OW AgendaBundle v1.1
:Requires: eZ Publish 5.3.x
:Author: Open Wide http://www.openwide.fr

Presentation
============

This extension provides a complete system to create and show events in a calendar view in front office via [FullCalendar](http://fullcalendar.io/).

LICENCE
-------
This eZ Publish extension is provided *as is*, in GPL v3 (see LICENCE).

Installation via composer
=========================

1. Add AgendaBundle in your project's composer.json

.. code-block:: json

    {
      "require": {
        "open-wide/ezpublish-agenda-bundle": "dev-master"
      }
    }


2. Enable the Bundle in your EzPublishKernel.php file:

.. code-block:: php

    <?php
    // ezpublish/EzPublishKernel.php
    use OpenWide\AgendaBundle;
    ...

    public function registerBundles()
    {
      $bundles = array(
        // ...
        new OpenWide\AgendaBundle\OpenWideAgendaBundle(),
      );
    }


3. Create the following classes using the content package in ``Package`` directory or using [OWMigration](https://github.com/Open-Wide/OWMigration):


* In the class group ``Agenda``
    * event_folder
    * event_liste
    * event_agenda
    * event_date

4. Add your event_folder LocationId in ``src/symfony/ezpublish/config/config.yml``:

.. code-block:: yml

        # LocationId of Agenda
        open_wide_agenda:
            root:
                location_id: ....
             # Nb of element per page
            paginate:
                max_per_page: ...

5. Create contents on back-office with the following structure:


    * event_folder
        * event_liste
        * event_agenda
            * event_date
        * event_agenda
            * event_date

6. Run the legacy bundle install script manually:

.. code-block:: sh

    $ php ezpublish/console ezpublish:legacybundles:install_extensions


By default, it will create an absolute symlink, but options exist to use a hard copy (–copy) or a relative link (--relative).




7. Add your event_folder LocationId in Legacy ini ``/settings/override/site.ini.append.php``:

.. code-block:: ini

    [AgendaSettings]
    RootFolderNodeId=...


8. Add this bundle on your assetic bundles array in ``src/symfony/ezpublish/config/config.yml``:


.. code-block:: yml

    # Assetic Configuration
    assetic:
        bundles:        [ OtherBundle, OpenWideAgendaBundle ]


9. Import ezpublish.yml configuration in ``src/symfony/ezpublish/config/ezpublish.yml``:


.. code-block:: yml

    imports:
    - {resource: @OpenWideAgendaBundle/Resources/config/ezpublish.yml}


10. Import routing.yml configuration in ``src/symfony/ezpublish/config/routing.yml``:


.. code-block:: yml

    agenda:
      resource: "@OpenWideAgendaBundle/Resources/config/routing.yml"


11. Regenerate the Assetic with the following command:


.. code-block:: sh

    $ php ezpublish/console assetic:dump web

12. Configure yours views in ``src/symfony/ezpublish/config/ezpublish.yml``:

.. code-block:: yml

    ezpublish:
        system:
            your-siteaccess:
                location_view:
                    event_folder:
                        template: OpenWideAgendaBundle:full:event_folder.html.twig
                        controller: "agenda.controller.event_folder.view:viewLocation"
                        match:
                            Identifier\ContentType: event_folder                                 

                    event_agenda:
                        template: OpenWideAgendaBundle:full:event_agenda.html.twig
                        controller: "agenda.controller.event_agenda.view:viewLocation"
                        match:
                            Identifier\ContentType: event_agenda

                    event_liste:
                        template: OpenWideAgendaBundle:full:event_liste.html.twig
                        controller: "agenda.controller.event_liste.view:viewLocation"
                        match:
                            Identifier\ContentType: event_liste
                line:
                    event_agenda:
                        template: OpenWideAgendaBundle:line:event_agenda.html.twig
                        controller: "agenda.controller.event_agenda.view:viewLocation"
                        match:
                            Identifier\ContentType: event_agenda             

                    event_date:
                        template: OpenWideAgendaBundle:line:event_date.html.twig
                        controller: "agenda.controller.event_date.view:viewLocation"
                        match:
                            Identifier\ContentType: event_date  
                bloc:
                    event_folder:
                        template: OpenWideAgendaBundle:bloc:event_folder.html.twig
                        controller: "agenda.controller.event_folder.view:viewLocation"
                        match:
                            Identifier\ContentType: event_folder                                
            content_view:
                embed_agenda:
                    event_date:
                        template: OpenWideAgendaBundle:content_view/embed:event_date.html.twig
                        controller: "agenda.controller.event_date.view:viewContent"
                        match:
                            Identifier\ContentType: event_date



Usage
=====

Front Office Calendar View
--------------------------
.. image:: https://github.com/Open-Wide/OWAgendaBundle/raw/master/doc/images/calendar.png


Front Office Calendar Mini View
--------------------------
.. image:: https://github.com/Open-Wide/OWAgendaBundle/raw/master/doc/images/calendar_mini.png


Other
=====

FullCalendar documentation: http://fullcalendar.io/docs/
