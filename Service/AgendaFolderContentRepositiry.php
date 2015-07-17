<?php

namespace OpenWide\Publish\AgendaBundle\Service;

use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

class AgendaFolderContentRepositiry extends ContentRepository
{

    const CHILDREN_TYPE = 'agenda';

    public function getJsonData( Location $location, $params = array() )
    {
        $agendaEventList = $this->getAgendaEventList( $location, $params );
        $contentJson = array();

        foreach( $agendaEventList as $agendaEvent )
        {
            $listeDates = $this->getAgendaEventContentRepository()->getSchedule( $agendaEvent, $params );
            foreach( $listeDates as $agendaSchedule )
            {
                $periodList = $this->getAgendaScheduleContentRepository()->getPeriods( $agendaSchedule );
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
        $time = time();
        $criteria = array(
            new Criterion\Subtree( $location->pathString ),
            new Criterion\ContentTypeIdentifier( array( 'agenda_event' ) ),
            new Criterion\Field( 'publish_start', Criterion\Operator::LT, $time ),
            new Criterion\Field( 'publish_end', Criterion\Operator::GT, $time ),
            new Criterion\Visibility( Criterion\Visibility::VISIBLE ),
        );

        $query = new LocationQuery();
        $query->filter = new Criterion\LogicalAnd( $criteria );

        $searchResult = $this->repository->getSearchService()->findLocations( $query );
        return $this->extractObjectsFromSearchResult( $searchResult );
    }

}
