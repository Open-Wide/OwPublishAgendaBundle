<?php

namespace OpenWide\Publish\AgendaBundle\Pagerfanta\Adapter;

use Pagerfanta\Adapter\AdapterInterface;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\SearchService;

class LocationQueryAdapter implements AdapterInterface
{

    /**
     * @var \eZ\Publish\API\Repository\SearchService
     */
    var $searchService;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\LocationQuery 
     */
    var $query;

    public function __construct( SearchService $searchService, LocationQuery $query )
    {
        $this->searchService = $searchService;
        $this->query = $query;
    }

    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    function getNbResults()
    {
        $query = clone $this->query;
        $query->limit = 0;
        return $this->searchService->findLocations( $query )->totalCount;
    }

    /**
     * Returns an slice of the results.
     *
     * @param integer $offset The offset.
     * @param integer $length The length.
     *
     * @return array|\Iterator|\IteratorAggregate The slice.
     */
    function getSlice( $offset, $length )
    {
        $query = clone $this->query;
        $query->limit = $length;
        $query->offset = $offset;
        $searchResult = $this->searchService->findLocations( $query );
        return $this->extractObjectsFromSearchResult( $searchResult );
    }

    /**
     * Extract location obects from search result objects
     * @param type $searchResult
     * @return type
     */
    protected function extractObjectsFromSearchResult( $searchResult )
    {
        $resultList = array();
        foreach( $searchResult->searchHits as $searchHit )
        {
            $resultList[] = $searchHit->valueObject;
        }

        return $resultList;
    }

}
