<?php

namespace OpenWide\Publish\AgendaBundle\Controller;

class AgendaScheduleViewController extends ViewController
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
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();

        $content = $contentService->loadContentByContentInfo( $location->getContentInfo() );

        $params = array(
            'location' => $location,
            'content' => $content,
            'agenda_schedule' => array(),
            'type' => 'normal'
        );

        $agenda_scheduleList = $this->getLegacyContentService()->getNodeList( array(
            'ParentNodeId' => $location->id,
            'ContentTypeIdentifier' => 'agenda_schedule'
                ) );

        /** @var \eZContentObjectTreeNode $date */
        foreach( $agenda_scheduleList as $date )
        {
            $params['agenda_schedule'][] = $date->ContentObjectID;
        }

        return $params;
    }

}
