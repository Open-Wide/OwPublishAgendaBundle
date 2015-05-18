<?php

namespace OpenWide\AgendaBundle\Controller;


class AgendaViewController extends ViewController
{
    protected function renderLocation( $location, $viewType, $layout = false, array $params = array() )
    {
        switch( $viewType ) {
            case 'full' :
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

        $params = array(
            'location' => $location,
            'content' => $content,
            'event_agenda' => array()
        );

        $nodeList = $this->getLegacyContentService()->fetchNodeList( array(
            'ParentNodeId' => $location->id,
            'ContentTypeIdentifier' => 'event_agenda'
        ) );

        $date = new \DateTime();
        $dateNow = $date->getTimestamp();

        foreach( $nodeList as $node ) {
            $nodeId = $node->attribute( 'node_id' );
            $eventAgenda  = array( 'nodeId' => $node->attribute( 'node_id' ) );

            // fetch 'event_date' childs
            $dateList = $this->getLegacyContentService()->fetchNodeList( array(
                'ParentNodeId' => $nodeId,
                'ContentTypeIdentifier' => 'event_date'
            ) );

            $hasDate = false;
            /** @var \eZContentObjectTreeNode $date */
            foreach( $dateList as $date ) {
                $dataMap = $date->ContentObject->dataMap();

                if( ( $dataMap['publish_start']->DataInt <= $dateNow ) && ( $dataMap['publish_end']->DataInt >= $dateNow ) ) {
                    $hasDate = true;
                    $eventAgenda['event_date'][] = $date->ContentObjectID;
                }
            }

            if ($hasDate) {
                $params['event_agenda'][$nodeId] = $eventAgenda;
            }
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
     * return OpenWide\AgendaBundle\Helper\FetchByLegacy
     */
    public function getLegacyContentService() {
        return $this->container->get( 'open_wide_agenda.fetch_by_legacy' );
    }
}
