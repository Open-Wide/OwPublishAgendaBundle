===========================================
OWAgendaBundle for eZ Publish documentation
===========================================

.. image:: https://github.com/Open-Wide/OWAgendaBundle/raw/master/doc/images/Open-Wide_logo.png
:align: center

:Extension: OW AgendaBundle v1.0
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

1. Clone the repository in the extension folder:

.. code-block:: sh

    $ git clone https://github.com/Open-Wide/OWAgendaBundle.git

2. Create the following classes using the content package in ``Package`` directory or using [OWMigration](https://github.com/Open-Wide/OWMigration):


* In the class group ``Agenda``
    * events_folder
    * event_agenda
    * event_date


3. Run the legacy bundle install script manually:

.. code-block:: sh

    $ php ezpublish/console [ezpublish:legacybundles:install_extensions]

 By default, it will create an absolute symlink, but options exist to use a hard copy (–copy) or a relative link (--relative).


4. Create contents on back-office with the following structure:


* events_folder
    * event_agenda
      * event_date
    * event_agenda
      * event_date


4. Add your events_folder LocationId in ``AgendaBundle/Resources/config/default_settings.yml``:

.. code-block:: yml

    parameters:
        # LocationId of Agenda
        ow_agenda.root.location_id: ...


5. Add your events_folder LocationId in Legacy ini ``AgendaBundle/ezpublish_legacy/owagendabundle/settings/site.ini.append.php``:

.. code-block:: ini

    [AgendaSettings]
    RootFolderNodeId=...


6. Add this bundle on your assetic bundles array in ``src/symfony/ezpublish/config/config.yml``:


.. code-block:: yml

    # Assetic Configuration
    assetic:
        bundles:        [ OtherBundle, OwAgendaBundle ]


7. Import ezpublish.yml configuration in ``src/symfony/ezpublish/config/ezpublish.yml``:


.. code-block:: yml

    imports:
        - {resource: @OwAgendaBundle/Resources/config/ezpublish.yml}


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
