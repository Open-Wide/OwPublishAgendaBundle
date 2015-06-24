<?php

namespace OpenWide\AgendaBundle\Controller;


class EventAgendaViewController extends ViewController
{
    protected function renderLocation( $location, $viewType, $layout = false, array $params = array() )
    {
        switch( $viewType ) {
            case 'full' :
            case 'line' :
                $params += $this->getViewFullParams($location);
                break;            
        }

        return parent::renderLocation( $location, $viewType, $layout, $params );
    }

    protected function getViewFullParams($location)
    {
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();

        $content = $contentService->loadContentByContentInfo( $location->getContentInfo() );
        $request = $this->getRequest();
        $liste = $request->query->get('liste', 0);        
        
        $image = $content->getFieldValue('image');
        $contentImage = $this->container->get('ow_intra_vendee.common_helper')->getImageByContentId($image->destinationContentId);

        $params = array(
            'location' => $location,
            'content' => $content,
            'event_date' => array(),
            'type' => 'normal',
            'image' => $contentImage,
            'liste' => $liste
        );

        $event_dateList = $this->getLegacyContentService()->fetchNodeList( array(
            'ParentNodeId' => $location->id,
            'ContentTypeIdentifier' => 'event_date'
        ) );

        /** @var \eZContentObjectTreeNode $date */
        foreach( $event_dateList as $date ) {
            $params['event_date'][] = $date->ContentObjectID;
        }

        return $params;
    }

    /**
     * Returns value for $parameterName and fallbacks to $defaultValue if not defined
     *
     * @param string $parameterName
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public function getConfigParameter( $parameterName, $namespace = null, $scope = null ) {
        if( $this->getConfigResolver()->hasParameter( $parameterName, $namespace, $scope ) ) {
            return $this->getConfigResolver()->getParameter( $parameterName, $namespace, $scope );
        }
    }

    /**
     * Checks if $parameterName is defined
     *
     * @param string $parameterName
     *
     * @return boolean
     */
    public function hasConfigParameter( $parameterName, $namespace = null, $scope = null ) {
        return $this->getConfigResolver()->hasParameter( $parameterName, $namespace, $scope );
    }

    /**
     * Return the legacy content service
     *
     * return OpenWide\Bundle\AgendaBundle\Helper\FetchByLegacy
     */
    public function getLegacyContentService() {
        return $this->container->get( 'open_wide_agenda.fetch_by_legacy' );
    }
}
