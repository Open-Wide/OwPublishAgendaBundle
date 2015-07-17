<?php

namespace OpenWide\Publish\AgendaBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;

class JsonController extends Controller
{

    public function eventsListJSONAction()
    {
        $repository = $this->getRepository();
        $request = $this->getRequest();

        $agendaLocationId = $request->query->get( 'locationId', $this->getConfigResolver('content.tree_root.location_id') );
        $agendaLocation = $repository->getLocationService()->loadLocation( $agendaLocationId );
        $params = array(
            'start' => $request->query->get( 'start', false ),
            'end' => $request->query->get( 'end', false )
        );

        $jsonData = $this->get('open_wide_publish_agenda.agenda_folder_content_repository')->getJsonData( $agendaLocation, $params );

        $response = new Response();
        $response->headers->set( 'Content-Type', 'application/json' );
        $response->headers->set( 'Access-Control-Allow-Origin', '*' );
        $response->headers->set( 'Access-Control-Expose-Headers', 'Cache-Control,Content-Encoding' );

        $response->setContent( json_encode( $jsonData ) );


        return $response;
    }

}
