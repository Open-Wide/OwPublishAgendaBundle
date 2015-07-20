<?php

namespace OpenWide\Publish\AgendaBundle\Service;

use Pagerfanta\Pagerfanta;
use OpenWide\Publish\AgendaBundle\Pagerfanta\Adapter\LocationQueryAdapter;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

class AgendaFolderContentRepository extends ContentRepository
{

    /**
     * @var \Pagerfanta\Pagerfanta
     */
    public $pagerfanta;

    /**
     * @var \OpenWide\Publish\AgendaBundle\Pagerfanta\Adapter\LocationQueryAdapter
     */
    public $adapter;

    const CHILDREN_TYPE = 'agenda';

    public function getJsonData( Location $location, $params = array() )
    {
        $agendaEventList = $this->getAgendaEventList( $location, $params );
        $contentJson = array();

        foreach( $agendaEventList as $agendaEvent )
        {
            $listDates = $this->getAgendaEventContentRepository()->getAgendaScheduleList( $agendaEvent, $params );
            foreach( $listDates as $agendaSchedule )
            {
                $periodList = $this->getAgendaScheduleContentRepository()->getPeriodList( $agendaSchedule );
                if( !$periodList )
                {
                    continue;
                }
                $contentJsonItem = array(
                    'title' => (string) $this->getTranslatedLocationName( $agendaEvent ),
                    'start' => $this->getFormattedDate( $agendaSchedule, 'start' ),
                    'url' => $this->getLocationUrl( $agendaEvent ),
                );
                $description = (string) $this->getTranslatedLocationFieldValue( $agendaEvent, 'subtitle' );
                if( empty( $description ) )
                {
                    $contentJsonItem['description'] = $contentJsonItem['title'];
                } else
                {
                    $contentJsonItem['description'] = $description;
                }
                foreach( $periodList as $period )
                {
                    $contentJsonItem['start'] = $period['start']->format( "o-m-dTH:i:s" );
                    $contentJsonItem['end'] = $period['end']->format( "o-m-dTH:i:s" );
                    $contentJson[] = $contentJsonItem;
                }
            }
        }
        return $contentJson;
    }

    public function getAgendaEventList( Location $location, $params = array() )
    {
        $criteria = $this->getAgendaEventListCriteria( $location, $params );

        $query = new LocationQuery();
        $query->filter = new Criterion\LogicalAnd( $criteria );

        $searchResult = $this->repository->getSearchService()->findLocations( $query );
        return $this->extractObjectsFromSearchResult( $searchResult );
    }

    public function getPaginatedAgendaEventList( Location $location, $params = array(), $page = 1, $maxPerPage = false )
    {
        if( !$maxPerPage )
        {
            $maxPerPage = $this->maxPerPage;
        }

        $criteria = $this->getAgendaEventListCriteria( $location, $params );

        $query = new LocationQuery();
        $query->filter = new Criterion\LogicalAnd( $criteria );

        $this->adapter = new LocationQueryAdapter( $this->repository->getSearchService(), $query );
        $this->pagerfanta = new Pagerfanta( $this->adapter );
        $this->pagerfanta->setMaxPerPage( intval( $maxPerPage ) );
        $this->pagerfanta->setCurrentPage( intval( $page ) );
        return $this->pagerfanta;
    }

    public function getAgendaEventListCriteria( Location $location, $params = array() )
    {
        $time = time();
        $criteria = array(
            new Criterion\Subtree( $location->pathString ),
            new Criterion\ContentTypeIdentifier( array( 'agenda_event' ) ),
            new Criterion\Field( 'publish_start', Criterion\Operator::LT, $time ),
            new Criterion\LogicalOr( array(
                new Criterion\Field( 'publish_end', Criterion\Operator::GT, time() ),
                new Criterion\Field( 'publish_end', Criterion\Operator::EQ, 0 )
                    ) ),
            new Criterion\Visibility( Criterion\Visibility::VISIBLE ),
        );
        return $criteria;
    }

}
