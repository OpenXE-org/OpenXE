<?php

namespace Xentral\Modules\Api\Controller\Version1;

use SimpleXMLElement;
use Xentral\Components\Http\Response;
use Xentral\Modules\Api\Exception\BadRequestException;
use Xentral\Modules\Api\Exception\InvalidArgumentException;
use Xentral\Modules\Api\Exception\ResourceNotFoundException;
use Xentral\Modules\Api\Resource\Result\CollectionResult;
use Xentral\Modules\Api\Resource\Result\ItemResult;

class AddressController extends AbstractController
{
    /**
     * Adressliste abrufen
     *
     * @example GET /v1/adressen
     *
     * @return Response
     */
    public function listAction()
    {
        // Kundennummer ist optional; dann nur eine Adresse zurückliefern
        $kundennummer = filter_var($this->request->get->get('kundennummer'), FILTER_SANITIZE_STRING);
        if (!empty($kundennummer)) {
            return $this->findByCustomerNumberAction($kundennummer);
        }

        // Optionale GET-Parameter
        $page = $this->getPaginationPage();
        $itemsPerPage = $this->getPaginationCount();

        // Limit und Offset aus Parameter berechnen
        $limit = $itemsPerPage;
        $offset = ($page - 1) * $itemsPerPage;

        $this->legacyApi->app->Secure->GET['action'] = 'AdresseListeGet';
        $this->legacyApi->app->Secure->GET['json'] = true;
        $this->legacyApi->app->Secure->POST['xml'] =
            '<xml>'.
            '<limit>'.$limit.'</limit>'.
            '<offset>'.$offset.'</offset>'.
            '<gruppen><kennziffer></kennziffer></gruppen>'. // @todo kennziffer
            '</xml>';

        /** @var SimpleXMLElement $xml */
        $xml = $this->legacyApi->ApiAdresseListeGet(true);
        $data = $this->converter->xmlToArray($xml);

        // Paginierung aus den Ergebnissen basteln
        $pagination = array();
        $pagination['items_per_page'] = $limit;
        $pagination['items_current'] = (int)$data['anz_result'];
        $pagination['items_total'] = (int)$data['anz_gesamt'];
        $pagination['page_current'] = (int)floor($offset / $limit) + 1;
        $pagination['page_last'] = (int)ceil($pagination['items_total'] / $limit);

        // Ergebnis aus alter API umstrukturieren
        $result = new CollectionResult($data['adresse'], $pagination);

        return $this->sendResult($result);
    }

    /**
     * Einzelne Adresse per ID abrufen
     *
     * @example GET /v1/adressen/999
     *
     * @return Response
     */
    public function readAction()
    {
        $id = $this->request->attributes->getInt('id');
        $data = $this->getAddressById($id);
        $result = new ItemResult($data);

        return $this->sendResult($result);
    }

    /**
     * @param string $number Kundennummer
     *
     * @return Response
     */
    public function findByCustomerNumberAction($number)
    {
        $data = $this->getAddressByCustomerNumber($number);
        $result = new ItemResult($data);

        return $this->sendResult($result);
    }

    /**
     * Adresse anlegen
     *
     * @example POST /v1/adressen
     *
     * @return Response
     */
    public function createAction()
    {
        // Request-Body in $_POST['json'] schreiben
        $requestBody = file_get_contents('php://input');
        $this->legacyApi->app->Secure->POST['json'] = $requestBody;
        $this->legacyApi->app->Secure->GET['action'] = 'AdresseCreate';

        // Adresse anlegen
        $customerNumber = $this->legacyApi->ApiAdresseCreate(true);

        if (intval($customerNumber) <= 0) {
            // @todo Nicht sehr hilfreiche Meldung
            // @todo Refaktorieren und besser Exception werfen
            throw new BadRequestException('Adresse konnte nicht angelegt werden.');
        }

        // Anlage war erfolgreich > Erzeugte Resource zurückliefern
        $data = $this->getAddressByCustomerNumber($customerNumber);
        $result = new ItemResult($data);

        return $this->sendResult($result);
    }

    /**
     * Adresse aktualisieren
     *
     * @example PUT /v1/adressen/999
     *
     * @return Response
     */
    public function updateAction()
    {
        $id = $this->request->attributes->getInt('id');

        // Request-Body zu XML konvertieren
        $requestBody = file_get_contents('php://input');
        $requestData = json_decode($requestBody, true);
        $requestData = array('adresse' => $requestData);
        $requestData['adresse']['id'] = (int)$id; // ID hinzufügen
        unset($requestData['adresse']['kundennummer']); // Kundennummer löschen, sonst wird evtl. die falsche Adresse aktualisiert // @todo Kundennummer änderbar machen
        $requestXml = $this->converter->arrayToXml($requestData);

        // Adresse ändern über alte API
        $this->legacyApi->app->Secure->GET['action'] = 'AdresseEdit';
        $this->legacyApi->app->Secure->GET['json'] = true;
        $this->legacyApi->app->Secure->POST['xml'] = $requestXml;
        $customerId = (int)$this->legacyApi->ApiAdresseEdit(true);

        if ($customerId <= 0) {
            // @todo Nicht sehr hilfreiche Meldung
            // @todo Refaktorieren und besser Exception werfen
            throw new BadRequestException('Adresse konnte nicht bearbeitet werden.');
        }

        // Bearbeiten war erfolgreich > Bearbeitete Resource zurückliefern
        $data = $this->getAddressById($customerId);
        $result = new ItemResult($data);

        return $this->sendResult($result);
    }

    /**
     * Einzelne Adresse per ID abrufen
     *
     * @param int $id
     *
     * @return array
     *
     * @throws \RuntimeException
     * @throws ResourceNotFoundException
     */
    protected function getAddressById($id)
    {
        if (intval($id) <= 0) {
            throw new InvalidArgumentException('Benötigter Parameter \'id\' ungültig.');
        }

        /** @var SimpleXMLElement $xml */
        $xml = $this->legacyApi->ApiAdresseGet(true, $id);
        if (empty($xml)) {
            throw new ResourceNotFoundException(sprintf('Adresse mit ID \'%s\' nicht gefunden', $id));
        }
        $data = $this->converter->xmlToArray($xml, true);

        // Ergebnis aus alter API umstrukturieren
        return $data;
    }

    /**
     * Einzene Adresse per Kundennummer abrufen
     *
     * @param string $kundennummer
     *
     * @return array
     *
     * @throws \RuntimeException
     * @throws ResourceNotFoundException
     */
    protected function getAddressByCustomerNumber($kundennummer)
    {
        if (empty($kundennummer)) {
            throw new InvalidArgumentException('Benötigter Parameter \'kundennummer\' ist leer.');
        }

        /** @var SimpleXMLElement $xml */
        $this->legacyApi->app->Secure->GET['kundennummer'] = $kundennummer;
        $xml = $this->legacyApi->ApiAdresseGet(true, '');
        if (empty($xml)) {
            throw new ResourceNotFoundException(sprintf('Adresse mit Kundennummer \'%s\' nicht gefunden', $kundennummer));
        }
        $data = $this->converter->xmlToArray($xml, true);

        // Ergebnis aus alter API umstrukturieren
        return ['data' => $data];
    }
}
