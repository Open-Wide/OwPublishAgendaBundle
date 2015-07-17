<?php

namespace OpenWide\Publish\AgendaBundle\Service;

use Pagerfanta\Pagerfanta;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use OpenWide\Publish\AgendaBundle\Pagerfanta\Adapter\LocationQueryAdapter;

class AgendaContentRepository extends ContentRepository
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

    /**
     * @var int
     */
    var $maxPerPage = 10;

    public function __construct( $container )
    {
        parent::__construct( $container );

        $this->maxPerPage = $this->getConfigParameter( 'open_wide_publish_agenda.paginate.max_per_page' );
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
