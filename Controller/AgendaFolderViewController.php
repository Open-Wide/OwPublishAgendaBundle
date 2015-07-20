<?php

namespace OpenWide\Publish\AgendaBundle\Controller;

class AgendaFolderViewController extends ViewController
{

    protected function renderLocation( $location, $viewType, $layout = false, array $params = array() )
    {
        switch( $viewType )
        {
            case 'full' :
                $params += $this->getViewFullParams( $location );
                break;
            case 'bloc' :
                $params += array( 'agendaLocationIdList' => $this->container->get( 'open_wide_publish_agenda.agenda_folder_content_repository' )->getAgendaLocationIdList( $location ) );
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
            $params['paginatedItems'] = $this->container->get( 'open_wide_publish_agenda.agenda_folder_content_repository' )->getPaginatedAgendaEventList( $location, $params, $currentPage );
        } else
        {
            $params['agendaLocationIdList'] = $this->container->get( 'open_wide_publish_agenda.agenda_folder_content_repository' )->getAgendaLocationIdList( $location );
        }
        return $params;
    }

}
