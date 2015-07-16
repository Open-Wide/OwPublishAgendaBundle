<?php

$Module = array( "name" => "OW Agenda" );

$ViewList = array();

$ViewList['index'] = array(
    'script' => 'index.php',
    'functions' => array( 'index' ),
    'default_navigation_part' => 'owagendanavigationpart',
    'params' => array()
);

$ViewList['calendar'] = array(
    'script' => 'calendar.php',
    'functions' => array( 'calendar' ),
    'default_navigation_part' => 'owagendanavigationpart',
    'params' => array()
);

$ViewList['archived_events'] = array(
    'script' => 'archived_events.php',
    'functions' => array( 'archived_events' ),
    'default_navigation_part' => 'owagendanavigationpart',
    'params' => array()
);

$FunctionList = array();
$FunctionList['index'] = array();
$FunctionList['calendar'] = array();
$FunctionList['archived_events'] = array();