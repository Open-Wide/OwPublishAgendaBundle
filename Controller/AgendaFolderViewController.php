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
            case 'calendar' :
                $params += $this->getViewCalendarParams( $location );
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
            $params['paginatedItems'] = $this->getAgendaFolderContentRepository()->getPaginatedAgendaEventList( $location, $params, $currentPage );
        } else
        {
            $params['agendaLocationIdList'] = $this->getAgendaFolderContentRepository()->getAgendaLocationIdList( $location );
        }
        return $params;
    }

    protected function getViewCalendarParams( $location )
    {
        return array( 'agendaLocationIdList' => $this->getAgendaFolderContentRepository()->getAgendaLocationIdList( $location ) );
    }

}
