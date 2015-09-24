<?php

namespace OpenWide\Publish\AgendaBundle\Repository\Content;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\Core\Repository\Values\Content\Location;

class AgendaEvent extends ContentRepository
{

    const CHILDREN_TYPE = 'agenda_schedule';

    public function getAgendaScheduleList( Location $location, $params = array() )
    {
        $criteria = $this->getAgendaScheduleListCriteria( $location, $params );
        $sortClause = array(
            new SortClause\Field( static::CHILDREN_TYPE, 'date_start', Query::SORT_ASC ),
            new SortClause\Field( static::CHILDREN_TYPE, 'hour_start', Query::SORT_ASC ),
            new SortClause\Field( static::CHILDREN_TYPE, 'date_end', Query::SORT_ASC ),
            new SortClause\Field( static::CHILDREN_TYPE, 'hour_end', Query::SORT_ASC )
        );
        
        if(isset($params['limit'])){
            $limit = $params['limit'];
        }else{
            $limit = null;
        }
        
        if(isset($params['offset'])){
            $offset = $params['offset'];
        }else{
            $offset = null;
        }        
        
        return $this->getLocationSearchResult( $criteria, $sortClause,$limit , $offset );
    }

    protected function getAgendaScheduleListCriteria( Location $location, $params = array() )
    {
        $time = time();
        $criteria = array(
            new Criterion\ParentLocationId( $location->id ),
            new Criterion\ContentTypeIdentifier( array( static::CHILDREN_TYPE ) ),
            new Criterion\Visibility( Criterion\Visibility::VISIBLE ),
        );

        if( isset( $params['start'] ) && isset( $params['end'] ) )
        {
            $startTime = strtotime( $params['start'] );
            $endTime = strtotime( $params['end'] ) + 24 * 60 * 60;
            $criteria[] = new Criterion\LogicalOr( array(
                new Criterion\LogicalAnd( array(
                    new Criterion\Field( 'date_start', Criterion\Operator::GTE, $startTime ),
                    new Criterion\Field( 'date_start', Criterion\Operator::LTE, $endTime )
                        ) ),
                new Criterion\LogicalAnd( array(
                    new Criterion\Field( 'date_end', Criterion\Operator::GTE, $startTime ),
                    new Criterion\Field( 'date_end', Criterion\Operator::LTE, $endTime )
                        ) ),
                new Criterion\LogicalAnd( array(
                    new Criterion\Field( 'date_start', Criterion\Operator::LTE, $startTime ),
                    new Criterion\Field( 'date_end', Criterion\Operator::GTE, $endTime )
                        ) )
                    ) );
        } elseif( isset( $params['start'] ) )
        {
            $time = strtotime( $params['start'] );
            $criteria[] = new Criterion\Field( 'date_start', Criterion\Operator::GTE, $time );
        } elseif( isset( $params['end'] ) )
        {
            $time = strtotime( $params['end'] ) + 24 * 60;
            $criteria[] = new Criterion\Field( 'date_end', Criterion\Operator::LTE, $time );
        }

        return $criteria;
    }

}
