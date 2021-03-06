<?php

namespace OpenWide\Publish\AgendaBundle\Repository\Content;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\DependencyInjection\ContainerAware;
use eZ\Publish\API\Repository\Values\Content\Field;

class ContentRepository extends ContainerAware
{

    /**
     * @var repository
     */
    protected $repository;

    /**
     * @var container
     */
    protected $container;

    /**
     * @var int
     */
    var $maxPerPage = 10;

    public function __construct( $container )
    {
        $this->container = $container;
        $this->repository = $this->container->get( 'ezpublish.api.repository' );
        
        $this->maxPerPage = $this->getConfigParameter( 'open_wide_publish_agenda.paginate.max_per_page' );
    }

    /**
     * 
     * @return OpenWide\Publish\AgendaBundle\Service\AgendaFolderContentRepository
     */
    public function getAgendaFolderContentRepository()
    {
        return $this->container->get( 'open_wide_publish_agenda.repository.content.agenda_folder' );
    }

    /**
     * 
     * @return OpenWide\Publish\AgendaBundle\Service\AgendaContentRepository
     */
    public function getAgendaContentRepository()
    {
        return $this->container->get( 'open_wide_publish_agenda.repository.content.agenda' );
    }

    /**
     * 
     * @return OpenWide\Publish\AgendaBundle\Service\AgendaEventContentRepository
     */
    public function getAgendaEventContentRepository()
    {
        return $this->container->get( 'open_wide_publish_agenda.repository.content.agenda_event' );
    }

    /**
     * 
     * @return OpenWide\Publish\AgendaBundle\Service\AgendaScheduleContentRepository
     */
    public function getAgendaScheduleContentRepository()
    {
        return $this->container->get( 'open_wide_publish_agenda.repository.content.agenda_schedule' );
    }

    /**
     * Return list of event sorted 
     * @param Location $location
     * @param type $maxPerPage
     * @param type $currentPage
     * @return type
     */
    public function getFolderChildrens( Location $location, $maxPerPage, $currentPage = 1 )
    {

        $criteria = array(
            new Criterion\ParentLocationId( $location->parentLocationId ),
            new Criterion\ContentTypeIdentifier( array( 'agenda_event' ) ),
            new Criterion\Visibility( Criterion\Visibility::VISIBLE ),
            new Criterion\Field( 'publish_start', Criterion\Operator::LT, time() ),
            new Criterion\LogicalOr( array(
                new Criterion\Field( 'publish_end', Criterion\Operator::GT, time() ), new Criterion\Field( 'publish_end', Criterion\Operator::EQ, 0 )
                    ) )
        );
        $query = new Query();
        $query->filter = new Criterion\LogicalAnd( $criteria );
        $query->sortClauses = array(
            $this->sortClauseAuto( $location )
        );

        $searchResult = $this->repository->getSearchService()->findContent( $query );

        $content = array();
        foreach( $searchResult->searchHits as $agendaEvent )
        {
            $listDates = $this->getChildren( $agendaEvent );
            foreach( $listDates->searchHits as $agendaSchedule )
            {
                $content[] = array(
                    'AgendaEvent' => $agendaEvent->valueObject->contentInfo->mainLocationId,
                    'AgendaSchedule' => $agendaSchedule->valueObject->contentInfo->mainLocationId,
                    'start' => $this->getFormattedDate( $agendaSchedule, 'order' )
                );
            }
        }

        usort( $content, array( $this, 'agendaSortMethod' ) );

        $result['offset'] = ($currentPage - 1) * $maxPerPage;
        $adapter = new ArrayAdapter( $content );
        $pagerfanta = new Pagerfanta( $adapter );

        $pagerfanta->setMaxPerPage( $maxPerPage );
        $pagerfanta->setCurrentPage( $currentPage );

        $result['prev_page'] = $pagerfanta->hasPreviousPage() ? $pagerfanta->getPreviousPage() : 0;
        $result['next_page'] = $pagerfanta->hasNextPage() ? $pagerfanta->getNextPage() : 0;
        $result['nb_pages'] = $pagerfanta->getNbPages();
        $result['items'] = $pagerfanta->getCurrentPageResults();
        $result['base_href'] = "?";
        $result['current_page'] = $pagerfanta->getCurrentPage();
        return $result;
    }

    /**
     * Sort tab with start field 
     * @param type $a
     * @param type $b
     * @return int
     */
    function agendaSortMethod( $a, $b )
    {
        if( $a['start'] == $b['start'] )
        {
            return 0;
        }
        return (intval( $a['start'] ) < intval( $b['start'] )) ? -1 : 1;
    }

    function getLocationSearchResult( array $criteria, array $sortClause = array(), $limit = null, $offset = null )
    {
        $query = new LocationQuery();
        $query->filter = new Criterion\LogicalAnd( $criteria );
        if( $sortClause )
        {
            $query->sortClauses = $sortClause;
        }
        
        if($limit){
            $query->limit = $limit;
        }
        if($offset){
            $query->offset = $offset;
        }
        
        $searchResult = $this->repository->getSearchService()->findLocations( $query );

        return $this->extractObjectsFromSearchResult( $searchResult );
    }

    function getLocationUrl( Location $location )
    {
        return $this->container->get( 'router' )->generate( $location );
    }

    /**
     * Renvoie le tri paramétré dans un node
     * @param Location $location
     * @return SectionName|LocationDepth|DateModified|Location\Priority|Location\PathString|ContentName|ContentId|DatePublished
     */
    public function sortClauseAuto( Location $location )
    {
        $sortField = $location->sortField;
        $sortOrder = $location->sortOrder == 1 ? Query::SORT_ASC : Query::SORT_DESC;
        switch( $sortField )
        {

            case 1 : // Fil d'Ariane
                return new SortClause\LocationPathString( $sortOrder );

            case 2 : // Date de création
                return new SortClause\DatePublished( $sortOrder );

            case 3 : // Date de modification
                return new SortClause\DateModified( $sortOrder );

            case 4 : // Section
                return new SortClause\SectionName( $sortOrder );

            case 5 : // Profondeur
                return new SortClause\Location\Depth( $sortOrder );

            case 6 : // Identifiant
                return new SortClause\ContentId( $sortOrder );

            case 7 : // Nom
                return new SortClause\ContentName( $sortOrder );

            case 8 : // Priorité
                return new SortClause\Location\Priority( $sortOrder );

            case 9 : // Nom du node
                return new SortClause\ContentName( $sortOrder );

            default :
                return new SortClause\Location\Priority( $sortOrder );
        }
    }

    function getFormattedDate( $location, $type )
    {
        switch( $type )
        {
            case 'duration':
                return date( "H:i", strtotime( $this->getTranslatedLocationFieldValue( $location, 'duration' ) ) );
            case 'order':
                return date( "Ymd", strtotime( $this->getTranslatedLocationFieldValue( $location, 'date_start' ) ) ) . date( "Hi", strtotime( $this->getTranslatedLocationFieldValue( $location, 'hour_start' ) ) );
            default: return "";
        }
    }

    /**
     * Return the Content object with the Id $contentId
     * @param int $contentId
     * @return Content
     */
    public function getContentByContentId( $contentId )
    {
        $content = null;
        if( $contentId )
        {
            $contentInfo = $this->repository->getContentService()->loadContentInfo( $contentId );
            $content = $this->repository->getContentService()->loadContentByContentInfo( $contentInfo );
        }
        return $content;
    }

    /**
     * Returns value for $parameterName and fallbacks to $defaultValue if not defined
     *
     * @param string $parameterName
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public function getConfigParameter( $parameterName, $namespace = null, $scope = null )
    {
        if( $this->container->get( 'ezpublish.config.resolver' )->hasParameter( $parameterName, $namespace, $scope ) )
        {
            return $this->container->get( 'ezpublish.config.resolver' )->getParameter( $parameterName, $namespace, $scope );
        }
    }

    /**
     * Checks if $parameterName is defined
     *
     * @param string $parameterName
     *
     * @return boolean
     */
    public function hasConfigParameter( $parameterName, $namespace = null, $scope = null )
    {
        return $this->container->get( 'ezpublish.config.resolver' )->hasParameter( $parameterName, $namespace, $scope );
    }

    /**
     * Get the translated field from a content object
     * 
     * @param \eZ\Publish\Core\Repository\Values\Content\Content $content
     * @param string $fieldIdentifier
     * @return \eZ\Publish\API\Repository\Values\Content\Field
     */
    protected function getTranslatedContentName( $content )
    {
        $translationHelper = $this->container->get( 'ezpublish.translation_helper' );
        return $translationHelper->getTranslatedContentName( $content );
    }

    /**
     * Get the translated field from a content object
     * 
     * @param \eZ\Publish\Core\Repository\Values\Content\Content $content
     * @param string $fieldIdentifier
     * @return \eZ\Publish\API\Repository\Values\Content\Field
     */
    protected function getTranslatedContentFieldValue( $content, $fieldIdentifier )
    {
        $translationHelper = $this->container->get( 'ezpublish.translation_helper' );
        $field = $translationHelper->getTranslatedField( $content, $fieldIdentifier );
        if( $field instanceof Field )
        {
            return $field->value;
        }
        return false;
    }

    /**
     * Get the selection field from a content object
     * 
     * @param \eZ\Publish\Core\Repository\Values\Content\Content $content
     * @param string $fieldIdentifier
     * @return \eZ\Publish\API\Repository\Values\Content\Field
     */
    protected function getTranslatedContentFieldSelection( $content, $fieldIdentifier )
    {
        $field = $content->getFieldValue($fieldIdentifier);
        if( is_object($field) )
        {
            return $field->selection;
        }
        return false;
    }
    
    /**
     * Get the translated field from a content object
     * 
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param string $fieldIdentifier
     * @return \eZ\Publish\API\Repository\Values\Content\Field
     */
    protected function getTranslatedLocationName( $location )
    {
        $content = $this->repository->getContentService()->loadContentByContentInfo( $location->getContentInfo() );
        return $this->getTranslatedContentName( $content );
    }

    /**
     * Get the translated field from a content object
     * 
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param string $fieldIdentifier
     * @return \eZ\Publish\API\Repository\Values\Content\Field
     */
    protected function getTranslatedLocationFieldValue( $location, $fieldIdentifier )
    {
        $content = $this->repository->getContentService()->loadContentByContentInfo( $location->getContentInfo() );
        return $this->getTranslatedContentFieldValue( $content, $fieldIdentifier );
    }

    /**
     * Get the selection field from a content object
     * 
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param string $fieldIdentifier
     * @return \eZ\Publish\API\Repository\Values\Content\Field
     */
    protected function getTranslatedLocationFieldSelection( $location, $fieldIdentifier )
    {
        $content = $this->repository->getContentService()->loadContentByContentInfo( $location->getContentInfo() );
        return $this->getTranslatedContentFieldSelection( $content, $fieldIdentifier );
    }

    protected function extractObjectsFromSearchResult( $searchResult )
    {
        $resultList = array();
        foreach( $searchResult->searchHits as $searchHit )
        {
            $resultList[] = $searchHit->valueObject;
            //print "<pre>".print_($searchHit->valueObject,true). "</pre>"; exit();
        }

        return $resultList;
    }

}
