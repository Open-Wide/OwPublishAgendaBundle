<?php

namespace OpenWide\Publish\AgendaBundle\Service;

use eZ\Publish\Core\Repository\Values\Content\Location;

class AgendaScheduleContentRepository extends ContentRepository
{

    const CHILDREN_TYPE = 'agenda_schedule';

    public function getPeriodList( Location $location )
    {
        $periodList = array();
        $startDate = $this->getTranslatedLocationFieldValue( $location, 'date_start' )->date;
        $startTime = $this->getTranslatedLocationFieldValue( $location, 'hour_start' )->time;
        $endDate = $this->getTranslatedLocationFieldValue( $location, 'date_end' )->date;
        if( !$endDate )
        {
            $endDate = clone $startDate;
        }
        $endTime = (string) $this->getTranslatedLocationFieldValue( $location, 'hour_end' )->time;
        $interval = new \DateInterval( 'P1D' );
        $dateRange = new \DatePeriod( $startDate, $interval, $endDate );
        foreach( $dateRange as $periodDate )
        {
            $startPeriod = clone $periodDate;
            $startPeriod->setTime( 0, 0, $startTime );
            $endPeriod = clone $periodDate;
            $endPeriod->setTime( 0, 0, $endTime );
            $periodList[] = array(
                'start' => $startPeriod,
                'end' => $endPeriod
            );
        }
        return $periodList;
    }

}
