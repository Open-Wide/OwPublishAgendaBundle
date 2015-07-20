<?php

namespace OpenWide\Publish\AgendaBundle\Controller;

class AgendaFolderViewController extends ViewController
{

    protected function renderLocation( $location, $viewType, $layout = false, array $params = array() )
    {
        switch( $viewType )
        {
            case 'full' :
                $params += array( 'type' => 'normal' );
                $params += $this->getViewFullParams( $location );
                break;
            case 'bloc' :
                $params += array( 'type' => 'mini' );
                break;
        }

        return parent::renderLocation( $location, $viewType, $layout, $params );
    }

    protected function getViewFullParams( $location )
    {
        $request = $this->getRequest();
        $displayType = $request->query->get( 'agendaDispayType', 'calendar' );
        $params = array(
            'agendaDispayType' => $displayType
        );
        if( $displayType == 'list' )
        {
            $currentPage = $request->query->get( 'page', 1 );
            $result = $this->container->get( 'open_wide_publish_agenda.agenda_folder_content_repository' )->getPaginatedAgendaEventList( $location, $params, $currentPage );
            $params['paginatedItems'] = $result;
        }
        return $params;
    }

}
