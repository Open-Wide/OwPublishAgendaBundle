<?php

$module = $Params['Module'];
$http = eZHTTPTool::instance();

$tpl = eZTemplate::factory();

$Result = array();
$Result['content'] = $tpl->fetch( "design:agenda/archived_events.tpl" );
$Result['left_menu'] = 'design:agenda/leftmenu.tpl';

$Result['path'] = array(
    array(
        'url' => 'agenda/archived_events',
        'text' => ezpI18n::tr( 'owagenda', 'Agenda' ) ) );
