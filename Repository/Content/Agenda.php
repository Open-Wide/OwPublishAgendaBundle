<?php

namespace OpenWide\Publish\AgendaBundle\Repository\Content;

use Pagerfanta\Pagerfanta;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use OpenWide\Publish\AgendaBundle\Pagerfanta\Adapter\LocationQueryAdapter;

class Agenda extends ContentRepository
{

    const CHILDREN_TYPE = 'agenda_event';

    /**
     * @var \Pagerfanta\Pagerfanta
     */
    public $pagerfanta;

    /**
     * @var \OpenWide\Publish\AgendaBundle\Pagerfanta\Adapter\LocationQueryAdapter
     */
    public $adapter;

    public function getJsonData( Location $location, $params = array() )
    {
        $agendaEventList = $this->getAgendaEventList( $location, $params );
        $contentJson = array();
        foreach( $agendaEventList as $agendaEvent )
        {
            $agendaScheduleList = $this->getAgendaEventContentRepository()->getAgendaScheduleList( $agendaEvent, $params );
            foreach( $agendaScheduleList as $agendaSchedule )
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
                    $contentJsonItem['start'] = $period['start']->format( "Y-m-d" ) . 'T' . $period['start']->format( "H:i:s" );
                    $contentJsonItem['end'] = $period['end']->format( "Y-m-dTH:i:s" ) . 'T' . $period['end']->format( "H:i:s" );
                    $contentJson[] = $contentJsonItem;
                }
            }
        }
        return $contentJson;
    }

    public function getAgendaEventList( Location $location, $params = array() )
    {
        $criteria = $this->getAgendaEventListCriteria( $location, $params );
        return $this->getLocationSearchResult( $criteria );
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

    protected function getAgendaEventListCriteria( Location $location, $params = array() )
    {
        $time = time();
        $criteria = array(
            new Criterion\ParentLocationId( $location->id ),
            new Criterion\ContentTypeIdentifier( array( static::CHILDREN_TYPE ) ),
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
