<?php

namespace OpenWide\Publish\AgendaBundle\Controller;

class AgendaEventViewController extends ViewController
{

    protected function renderLocation( $location, $viewType, $layout = false, array $params = array() )
    {
        switch( $viewType )
        {
            case 'full' :
            case 'line' :
                $params += $this->getViewFullParams( $location );
                break;
        }

        return parent::renderLocation( $location, $viewType, $layout, $params );
    }

    protected function getViewFullParams( $location )
    {
        
        return array(
            'agendaScheduleList' => $this->container->get( 'open_wide_publish_agenda.agenda_event_content_repository' )->getAgendaScheduleList( $location )
        );
    }

}
