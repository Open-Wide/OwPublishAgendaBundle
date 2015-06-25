{def $timestamp = currentdate()
     $agenda_root_node_id = ezini( 'AgendaSettings', 'RootFolderNodeId', 'site.ini' )
     $node = fetch('content', 'node', hash(
        'node_id', array($agenda_root_node_id),
        'class_filter_type', 'include',
        'class_filter_array', array( 'event' )
) )}

<div class="agenda">
    <div class="border-box">
        <div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
        <div class="border-ml">
            <div class="border-mr">
                <div class="border-mc float-break">
                    {include uri='design:node/view/full.tpl'}
                </div>
            </div>
        </div>
        <div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
    </div>
</div>

