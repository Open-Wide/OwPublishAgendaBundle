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
        $contentImage = null;
        $image = $this->getTranslatedLocationFieldValue( $location, 'image' );
        if( $image )
        {
            $contentImage = $this->getAgendaEventContentRepository()->getContentByContentId( $image->destinationContentId );
        }
        return array(
            'agendaScheduleList' => $this->getAgendaEventContentRepository()->getAgendaScheduleList( $location ),
            'contentImage' => $contentImage
        );
    }

}
