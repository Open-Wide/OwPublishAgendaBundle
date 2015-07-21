<?php

namespace OpenWide\Publish\AgendaBundle\Controller;

class AgendaViewController extends ViewController
{

    protected function renderLocation( $location, $viewType, $layout = false, array $params = array() )
    {
        switch( $viewType )
        {
            case 'full' :
                $params += $this->getViewFullParams( $location );
                break;
        }

        return parent::renderLocation( $location, $viewType, $layout, $params );
    }

    protected function getViewFullParams( $location )
    {
        $request = $this->getRequest();

        $displayType = $request->query->get( 'agendaDispayType', 'calendar' );
        $params = array(
            'agendaDispayType' => $displayType,
            'type' => 'normal'
        );
        if( $displayType == 'list' )
        {
            $currentPage = $request->query->get( 'page', 1 );
            $result = $this->getAgendaContentRepository()->getPaginatedAgendaEventList( $location, $params, $currentPage );
            $params['paginatedItems'] = $result;
        }
        return $params;
    }

}
