{def $agenda_folder_node_id = ezini( 'AgendaSettings', 'FolderNodeId', 'owpagenda.ini' )
     $node = fetch('content', 'node', hash(
        'node_id', $agenda_folder_node_id
) )}

<div class="agenda">
    <div class="border-box">
        <div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
        <div class="border-ml">
            <div class="border-mr">
                <div class="border-mc float-break">
                    {node_view_gui content_node=$node view='full'}
                </div>
            </div>
        </div>
        <div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
    </div>
</div>

