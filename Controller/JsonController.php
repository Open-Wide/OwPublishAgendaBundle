<?php

namespace OpenWide\Bundle\PublishAgendaBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;

class JsonController extends Controller {

    public function eventsListJSONAction() {
        $repository = $this->getRepository();
        $request = $this->getRequest();

        $agendaLocationId = $request->query->get('locationId', 0);
        $agendaLocation = $repository->getLocationService()->loadLocation($agendaLocationId);
        $admin = $request->query->get('admin', 0);

        $criteria = array(
            new Criterion\ParentLocationId($agendaLocation->contentInfo->mainLocationId),
            new Criterion\ContentTypeIdentifier(array('event_agenda')),
            new Criterion\Field('publish_start', Criterion\Operator::LT, time()),
            new Criterion\Field('publish_end', Criterion\Operator::GT, time()),
            new Criterion\Visibility(Criterion\Visibility::VISIBLE),
        );

        $query = new Query();
        $query->filter = new Criterion\LogicalAnd($criteria);

        $searchResult = $repository->getSearchService()->findContent($query);

        $date = new \DateTime();
        $dateNow = $date->getTimestamp();
        $content = array();

        foreach ($searchResult->searchHits as $searchHit) {
            $listeDates = $this->get('open_wide_agenda.fetch_by_legacy')->getChildren($searchHit);
            foreach ($listeDates->searchHits as $eventDate) {
                $content[] = array(
                    'title' => $searchHit->valueObject->getFieldValue('title')->__toString(),
                    'description' => $searchHit->valueObject->getFieldValue('subtitle')->__toString(),
                    'start' => $this->get('open_wide_agenda.fetch_by_legacy')->childrenFormattedDate($eventDate, 'start'),
                    'end' => $this->get('open_wide_agenda.fetch_by_legacy')->childrenFormattedDate($eventDate, 'end'),
                    'duration' => $this->get('open_wide_agenda.fetch_by_legacy')->childrenFormattedDate($eventDate, 'duration'),
                    'url' => $this->getUrl($searchHit,$admin),
                );
            }
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Expose-Headers', 'Cache-Control,Content-Encoding');

        $response->setContent(json_encode($content));
        

        return $response;
    }


    function getUrl($value,$admin) {
        $LocationId = $value->valueObject->versionInfo->contentInfo->mainLocationId;
        $locationService = $this->getRepository()->getLocationService();
        $hitLocation = $locationService->loadLocation($LocationId);
        $prefix = $admin==1?"/Accueil-du-site":"";
        $url = $prefix.$this->generateUrl($hitLocation);

        return $url;
    }

}
