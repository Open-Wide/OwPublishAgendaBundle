<?php

$module = $Params['Module'];
$http = eZHTTPTool::instance();

$tpl = eZTemplate::factory();

$Result = array();
$Result['content'] = $tpl->fetch( "design:agenda/calendar.tpl" );
$Result['left_menu'] = 'design:agenda/leftmenu.tpl';

$Result['path'] = array(
    array(
        'url' => 'agenda/calendar',
        'text' => ezpI18n::tr( 'owagenda', 'Agenda' ) ));