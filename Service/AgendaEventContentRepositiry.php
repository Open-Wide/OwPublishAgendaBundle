<?php

namespace OpenWide\Publish\AgendaBundle\Service;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\Core\Repository\Values\Content\Location;

class AgendaEventContentRepositiry extends ContentRepository
{

    const CHILDREN_TYPE = 'agenda_schedule';

    public function getSchedule( Location $location, $params = array() )
    {
        $criteria = array();
        if( isset( $params['start'] ) && isset( $params['end'] ) )
        {
            $startTime = strtotime( $params['start'] );
            $endTime = strtotime( $params['end'] ) + 24 * 60;
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
        return $this->getChildren( $location, $criteria );
    }

}
