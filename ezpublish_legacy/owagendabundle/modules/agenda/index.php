<?php

$module = $Params['Module'];
$http = eZHTTPTool::instance();

$tpl = eZTemplate::factory();

$Result = array();
$Result['content'] = $tpl->fetch( "design:agenda/index.tpl" );
$Result['left_menu'] = 'design:agenda/leftmenu.tpl';

$Result['path'] = array(
    array(
        'url' => 'agenda/index',
        'text' => ezpI18n::tr( 'owagenda', 'Agenda' ) ));
