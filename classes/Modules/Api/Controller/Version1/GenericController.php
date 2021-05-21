<?php

namespace Xentral\Modules\Api\Controller\Version1;

use Xentral\Components\Http\Response;

class GenericController extends AbstractController
{
    /**
     * Resourcen-Liste abrufen
     *
     * @return Response
     */
    public function listAction()
    {
        // Filter, Sortierung und Paginierung
        $filter = $this->prepareFilterParams();
        $sorting = $this->prepareSortingParams();
        $includes = $this->prepareIncludeParams();
        $currentPage = $this->getPaginationPage();
        $itemsPerPage = $this->getPaginationCount();

        // Liste laden
        $resource = $this->getResource($this->resourceClass);
        $result = $resource->getList($filter, $sorting, [], $includes, $currentPage, $itemsPerPage);

        return $this->sendResult($result);
    }

    /**
     * Einzelne Resource anhand ID laden
     *
     * @return Response
     */
    public function readAction()
    {
        $resource = $this->getResource($this->resourceClass);
        $includes = $this->prepareIncludeParams();

        $id = $this->getResourceId();
        $result = $resource->getOne($id, $includes);

        return $this->sendResult($result);
    }

    /**
     * Resource anlegen
     *
     * @return Response
     */
    public function createAction()
    {
        $resource = $this->getResource($this->resourceClass);

        $input = $this->getRequestData();
        $result = $resource->insert($input);

        return $this->sendResult($result, Response::HTTP_CREATED);
    }

    /**
     * Resource ändern
     *
     * @return Response
     */
    public function updateAction()
    {
        $resource = $this->getResource($this->resourceClass);

        $id = $this->getResourceId();
        $resource->checkOrFail($id);

        $input = $this->getRequestData();
        $result = $resource->edit($id, $input);

        return $this->sendResult($result);
    }

    /**
     * Resource löschen
     *
     * @return Response
     */
    public function deleteAction()
    {
        $resource = $this->getResource($this->resourceClass);

        $id = $this->getResourceId();
        $resource->checkOrFail($id);

        $result = $resource->delete($id);

        return $this->sendResult($result);
    }
}
