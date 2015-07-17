<?php

namespace OpenWide\Publish\AgendaBundle\Controller;

class AgendaFolderViewController extends ViewController
{

    protected function renderLocation( $location, $viewType, $layout = false, array $params = array() )
    {
        switch( $viewType )
        {
            case 'full' :
            case 'bloc' :
                $params += $this->getViewFullParams( $location, $viewType );
                break;
        }

        return parent::renderLocation( $location, $viewType, $layout, $params );
    }

    protected function getViewFullParams( $location, $viewType )
    {
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();

        $content = $contentService->loadContentByContentInfo( $location->getContentInfo() );

        $params = array(
            'location' => $location,
            'content' => $content,
            'type' => ($viewType == 'full' ? 'normal' : 'mini')
        );

        $agenda_scheduleList = $this->getLegacyContentService()->getNodeList( array(
            'ParentNodeId' => $location->id,
            'ContentTypeIdentifier' => 'agenda_schedule'
                ) );

        return $params;
    }

}
