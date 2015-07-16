{def $agenda_root_node_id = ezini( 'AgendaSettings', 'FolderNodeId', 'owpagenda.ini' )
     $node = fetch('content', 'node', hash(
        'node_id', $agenda_root_node_id,
        'class_filter_type', 'include',
        'class_filter_array', array( 'event' )
) )}

<style type="text/css" media="screen">
    @import url({"stylesheets/fullcalendar.min.css"|ezdesign});
</style>

{ezcss_require( array(
                        'open_wide_publish_agenda_admin.css',
                        'fullcalendar.css' ) )}

{ezscript_require( array(   'bootstrap.min.js',
                            'moment.min.js',
                            'fullcalendar.min.js',
                            'fullcalendar_fr.js',
                            'fonctions.js') )}

{*{ezscript_require( array(   'jquery-2.1.3.min.js',*}
                            {*'bootstrap.min.js',*}
                            {*'moment.min.js',*}
                            {*'fullcalendar.min.js',*}
                            {*'fullcalendar_fr.js',*}
                            {*'fonctions.js') )}*}

<div class="agenda">
    <div class="border-box">
        <div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
        <div class="border-ml">
            <div class="border-mr">
                <div class="border-mc float-break">
                    <div class="context-block">
                        <div class="box-header">
                            <div class="box-tc">
                                <div class="box-ml">
                                    <div class="box-mr">
                                        <div class="box-tl">
                                            <div class="box-tr">
                                                <h1 class="context-title">{'Events calendar'|i18n('design/admin/templates/agenda/calendar')}</h1>
                                                <div class="header-mainline"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-bc">
                            <div class="box-ml">
                                <div class="box-mr">
                                    <div class="box-bl">
                                        <div class="box-br">
                                            <div class="box-content">
                                                {* Calendar call *}
                                                <div id='calendar'></div>
                                                <input type="hidden" name="locationId" id="locationId" value="{$agenda_root_node_id}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
    </div>
</div>