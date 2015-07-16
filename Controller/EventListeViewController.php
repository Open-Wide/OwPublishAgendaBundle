<?php

namespace OpenWide\Bundle\PublishAgendaBundle\Controller;

class EventListeViewController extends ViewController
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
        /* @var $location eZ\Publish\Core\Repository\Values\Content\Location */
        $repository = $this->getRepository();
        $request = $this->getRequest();
        $contentService = $repository->getContentService();
        $content = $contentService->loadContentByContentInfo( $location->getContentInfo() );
        
        $params = array(
            'location' => $location,
            'content' => $content,
            'type' => 'normal'
        );
        
        $currentPage = $request->query->get('page', 1);

        $result = $this->container->get('open_wide_publish_agenda.fetch_by_legacy')->getFolderChildrens(
                    $location, 
                    $this->container->getParameter('open_wide_publish_agenda.paginate.max_per_page'), 
                    $currentPage
            );        
        
        $params['items'] = $result['items'];
        $params['current_page'] = $result['current_page'];
        $params['nb_pages'] = $result['nb_pages'];
        $params['prev_page'] = $result['prev_page'];
        $params['next_page'] = $result['next_page'];
        $params['href_pagination'] = $result['base_href'];

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
     * return OpenWide\Bundle\PublishAgendaBundle\Helper\FetchByLegacy
     */
    public function getLegacyContentService() {
        return $this->container->get( 'open_wide_publish_agenda.fetch_by_legacy' );
    }
}
