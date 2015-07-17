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
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();

        $content = $contentService->loadContentByContentInfo( $location->getContentInfo() );
        $request = $this->getRequest();
        $liste = $request->query->get( 'liste', 0 );

        $image = $content->getFieldValue( 'image' );
        $contentImage = $this->getAgendaContentService()->getImageByContentId( $image->destinationContentId );

        $params = array(
            'location' => $location,
            'content' => $content,
            'agenda_schedule' => array(),
            'type' => 'normal',
            'image' => $contentImage,
            'liste' => $liste
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
