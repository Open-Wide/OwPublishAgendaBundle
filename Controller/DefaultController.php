<?php

namespace OpenWide\AgendaBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $templateName = $this->container->getParameter( 'openwide_agenda.template.index' );

        $type = 'normal';
        return $this->render( $templateName, array( 'type' => $type ) );
    }

    public function indexMiniAction()
    {
        $templateName = $this->container->getParameter( 'openwide_agenda.template.indexmini' );

        $type = 'mini';
        return $this->render( $templateName, array( 'type' => $type ) );
    }

    public function eventsListJSONAction()
    {
        $repository = $this->getRepository();
        $agendaLocationId = $this->container->getParameter( 'openwide_agenda.root.location_id' );
        $agendaLocation = $repository->getLocationService()->loadLocation( $agendaLocationId );

        $criteria = array(
            new Criterion\ParentLocationId( $agendaLocation->contentInfo->mainLocationId ),
            new Criterion\ContentTypeIdentifier( array( 'event_agenda' ) ),
            new Criterion\Visibility( Criterion\Visibility::VISIBLE ),
        );

        $query = new Query();
        $query->filter = new Criterion\LogicalAnd( $criteria );

        $searchResult = $repository->getSearchService()->findContent( $query );

        $date = new \DateTime();
        $dateNow = $date->getTimestamp();
        $content = array();

        foreach ( $searchResult->searchHits as $searchHit ) {

            if ( ( $this->childrenFormattedDate( $searchHit, 'publish_start') <= $dateNow ) && ( $this->childrenFormattedDate( $searchHit, 'publish_end') >= $dateNow ) ) {
                $content[] = array(
                    'title' => $searchHit->valueObject->getFieldValue('subtitle')->__toString(),
                    'description' => $this->render_xml_data( $searchHit->valueObject->getFieldValue('description') ),
                    'start' => $this->childrenFormattedDate( $searchHit, 'start' ),
                    'end' => $this->childrenFormattedDate( $searchHit, 'end' ),
                    'duration' => $this->childrenFormattedDate( $searchHit, 'duration' ),
                );
            }

            // eZ default event class binding
//            $content[] = array(
//                'title' => $searchHit->valueObject->getFieldValue('short_title')->__toString(),
//                'description' => $this->xmlToString( $searchHit->valueObject->getFieldValue('text') ),
//                'start' => $this->timeStampToISO( $searchHit->valueObject->getFieldValue('from_time')->__toString() ),
//                'end' => $this->timeStampToISO( $searchHit->valueObject->getFieldValue('to_time')->__toString() ),
//                'className' => strtolower( $searchHit->valueObject->getFieldValue('category')->__toString() ),
//                'tags' => $searchHit->valueObject->getFieldValue('tags')
//            );
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $response->setContent( json_encode( $content ) );

        return $response;
    }

    function childrenFormattedDate( $parentNodeId, $type ) {

        $repository = $this->getRepository();

        $criteria = array(
            new Criterion\ParentLocationId( $parentNodeId->valueObject->contentInfo->mainLocationId ),
            new Criterion\ContentTypeIdentifier( array( 'event_date' ) ),
            new Criterion\Visibility( Criterion\Visibility::VISIBLE ),
        );

        $query = new Query();
        $query->filter = new Criterion\LogicalAnd( $criteria );

        $searchResult = $repository->getSearchService()->findContent( $query );

        switch ( $type ) {
            case 'start':
                foreach ( $searchResult->searchHits as $searchHit ) {
                    // ISO 8601 date_start
                    return date("o-m-d", strtotime( $searchHit->valueObject->getFieldValue('date_start') ) ) . 'T'. date("H:i:s", strtotime( $searchHit->valueObject->getFieldValue('hour_start') ) );
                }
                break;
            case 'end':
                foreach ( $searchResult->searchHits as $searchHit ) {
                    // ISO 8601 date_end
                    return date("o-m-d", strtotime( $searchHit->valueObject->getFieldValue('date_end') ) ) . 'T'. date("H:i:s", strtotime( $searchHit->valueObject->getFieldValue('hour_end') ) );
                }
                break;
            case 'duration':
                foreach ( $searchResult->searchHits as $searchHit ) {
                    return date("H:i", strtotime( $searchHit->valueObject->getFieldValue('duration') ) );
                }
                break;
            case 'publish_start':
                foreach ( $searchResult->searchHits as $searchHit ) {
                    return $searchHit->valueObject->getFieldValue('publish_start')->__toString();
                }
                break;
            case 'publish_end':
                foreach ( $searchResult->searchHits as $searchHit ) {
                    return $searchHit->valueObject->getFieldValue('publish_end')->__toString();
                }
                break;
        }
    }

    function timeStampToISO( $timestamp_chaine ) {
        $iso8601 = date('c', $timestamp_chaine);

        return $iso8601;
    }

    function render_xml_data( $content ){
        $content = new \SimpleXMLElement( $content );

        return (string) $content->paragraph;
    }
}
