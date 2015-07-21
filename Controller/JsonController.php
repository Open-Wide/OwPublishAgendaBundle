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

        $locationId = $request->query->get( 'locationId', $this->getConfigResolver( 'content.tree_root.location_id' ) );
        $location = $repository->getLocationService()->loadLocation( $locationId );

        $contentTypeService = $repository->getContentTypeService();
        $contentType = $contentTypeService->loadContentType( $location->getContentInfo()->contentTypeId );

        $params = array(
            'start' => $request->query->get( 'start', false ),
            'end' => $request->query->get( 'end', false )
        );

        switch( $contentType->identifier )
        {
            case 'agenda_folder':
                $jsonData = $this->getAgendaFolderContentRepository()->getJsonData( $location, $params );
                break;
            case 'agenda':
                $jsonData = $this->getAgendaContentRepository()->getJsonData( $location, $params );
                break;
            default:
                $jsonData = array();
        }

        $response = new Response();
        $response->headers->set( 'Content-Type', 'application/json' );
        $response->headers->set( 'Access-Control-Allow-Origin', '*' );
        $response->headers->set( 'Access-Control-Expose-Headers', 'Cache-Control,Content-Encoding' );

        $response->setContent( json_encode( $jsonData ) );


        return $response;
    }

    /**
     * 
     * @return OpenWide\Publish\AgendaBundle\Service\AgendaFolderContentRepository
     */
    public function getAgendaFolderContentRepository()
    {
        return $this->get( 'open_wide_publish_agenda.agenda_folder_content_repository' );
    }

    /**
     * 
     * @return OpenWide\Publish\AgendaBundle\Service\AgendaContentRepository
     */
    public function getAgendaContentRepository()
    {
        return $this->get( 'open_wide_publish_agenda.agenda_content_repository' );
    }

}
