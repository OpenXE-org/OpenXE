<?php

namespace Xentral\Modules\Api\Controller\Version1;

use Xentral\Components\Http\Response;
use Xentral\Modules\Api\Exception\BadRequestException;
use Xentral\Modules\Api\Exception\ValidationErrorException;

/**
 * Controller zum Anlegen und Bearbeiten von Trackingnummern
 *
 * Die Auflistung der Trackingnummer-Ressource wird über den GenericController behandelt.
 */
class TrackingNumberController extends AbstractController
{
    /**
     * Trackingsnummer anlegen
     *
     * @return Response
     */
    public function createAction()
    {
        $input = $this->getRequestData();
        $errors = [];

        // Pflichtfelder prüfen
        if (empty($input['tracking'])) {
            $errors[] = 'Required field "tracking" is empty.';
        }
        if (empty($input['internet']) && empty($input['auftrag']) && empty($input['lieferschein'])) {
            $errors[] =
                'Required fields "internet", "auftrag" and "lieferschein" are empty. ' .
                'One of them has to be filled.';
        }
        if (empty($input['gewicht'])) {
            $errors[] = 'Required field "gewicht" is empty.';
        }
        if (empty($input['anzahlpakete'])) {
            $errors[] = 'Required field "anzahlpakete" is empty.';
        }
        if (empty($input['versendet_am'])) {
            $errors[] = 'Required field "versendet_am" is empty.';
        }

        // Nach Pflichtfeld-Prüfung vorab Fehler anzeigen
        if (count($errors) > 0) {
            throw new ValidationErrorException($errors);
        }

        // Format der Pflichtfelder prüfen
        $input['versendet_am'] = $this->ensureShippingDateFormat($input['versendet_am']);
        $input['anzahlpakete'] = $this->ensureParcelCountFormat($input['anzahlpakete']);

        // Prüfen ob Auftragsdaten gültig
        $orderData = $this->ensureOrderData($input['lieferschein'], $input['auftrag'], $input['internet']);

        // Trackingnummer-Eintrag anlegen
        $resource = $this->getResource($this->resourceClass);
        $bindValues = [
            'adresse'       => $orderData['adresseid'],
            'lieferschein'  => $orderData['lieferscheinid'],
            'projekt'       => $orderData['projektid'],
            'firma'         => $orderData['firmenid'],
            'gewicht'       => $input['gewicht'],
            'anzahlpakete'  => $input['anzahlpakete'],
            'versendet_am'  => $input['versendet_am'],
            'tracking'      => $input['tracking'],
            'abgeschlossen' => 1,
        ];
        $result = $resource->insert($bindValues);

        return $this->sendResult($result, Response::HTTP_CREATED);
    }

    /**
     * Trackingnummer bearbeiten
     *
     * @return Response
     */
    public function updateAction()
    {
        $resource = $this->getResource($this->resourceClass);

        $id = $this->getResourceId();
        $resource->checkOrFail($id);

        $input = $this->getRequestData();
        $updateData = [];

        // Format prüfen
        if (isset($input['versendet_am'])) {
            $updateData['versendet_am'] = $this->ensureShippingDateFormat($input['versendet_am']);
        }
        if (isset($input['anzahlpakete'])) {
            $updateData['anzahlpakete'] = $this->ensureParcelCountFormat($input['anzahlpakete']);
        }

        if (isset($input['gewicht'])) {
            $updateData['gewicht'] = (string)$input['gewicht'];
        }
        if (isset($input['tracking'])) {
            $updateData['tracking'] = (string)$input['tracking'];
        }

        // Prüfen ob Auftragsdaten gültig
        if (isset($input['lieferschein']) || isset($input['auftrag']) || isset($input['internet'])) {
            $orderData = $this->ensureOrderData($input['lieferschein'], $input['auftrag'], $input['internet']);

            $updateData['adresse'] = $orderData['adresseid'];
            $updateData['lieferschein'] = $orderData['lieferscheinid'];
            $updateData['projekt'] = $orderData['projektid'];
            $updateData['firma'] = $orderData['firmenid'];
        }

        if (empty($updateData)) {
            throw new BadRequestException('Payload is empty.');
        }
        $result = $resource->edit($id, $updateData);

        return $this->sendResult($result);
    }

    /**
     * Prüft ob Auftragsdaten gültig und gibt diese zurück
     *
     * @param string|null $deliveryNoteNumber Lieferscheinnummer
     * @param string|null $orderNumber        Auftragsnummer
     * @param string|null $internetNumber     Internetnummer aus Auftrag
     *
     * @throws ValidationErrorException
     *
     * @return array
     */
    protected function ensureOrderData($deliveryNoteNumber = null, $orderNumber = null, $internetNumber = null)
    {
        $orderData = [];

        if (!empty($deliveryNoteNumber)) {
            $orderData = $this->ensureOrderDataByDeliveryNoteNumber($deliveryNoteNumber);
        }
        if (!empty($orderNumber)) {
            $orderData = $this->ensureOrderDataByOrderNumber($orderNumber);
        }
        if (!empty($internetNumber)) {
            $orderData = $this->ensureOrderDataByInternetNumber($internetNumber);
        }
        if (count($orderData) === 0) {
            throw new ValidationErrorException(['Could not find order data.']);
        }

        return $orderData;
    }

    /**
     * Auftrag anhand der Internetnummer (im Auftrag) finden
     *
     * @param string $internetNumber
     *
     * @throws ValidationErrorException
     *
     * @return array
     */
    protected function ensureOrderDataByInternetNumber($internetNumber)
    {
        $order = $this->db->fetchAll(
            'SELECT 
               au.id AS auftragsid, 
               au.projekt AS projektid,
               au.adresse AS adresseid, 
               au.belegnr AS auftragsnummer, 
               au.internet AS internetnummer, 
               au.firma AS firmenid
             FROM auftrag AS au 
             WHERE au.internet = :internetnummer',
            ['internetnummer' => $internetNumber]
        );
        if (count($order) === 0) {
            throw new ValidationErrorException([
                sprintf('Order not found with internet number "%s".', $internetNumber),
            ]);
        }
        if (count($order) > 1) {
            throw new ValidationErrorException([
                sprintf('Logic error: Found more than one order with internet number "%s".', $internetNumber),
            ]);
        }

        $orderData = $order[0];

        $deliveryNotes = $this->db->fetchAll(
            'SELECT l.id AS lieferscheinid , l.belegnr AS lieferscheinnummer
             FROM lieferschein AS l 
             WHERE l.auftragid = :order_id',
            ['order_id' => $orderData['auftragsid']]
        );
        if (count($deliveryNotes) === 0) {
            throw new ValidationErrorException([
                sprintf('Delivery note not found for internet number "%s".', $internetNumber),
            ]);
        }
        if (count($deliveryNotes) > 1) {
            throw new ValidationErrorException([
                sprintf('Logic error: Found more than one delivery note for internet number "%s".', $internetNumber),
            ]);
        }

        $orderData['lieferscheinid'] = $deliveryNotes[0]['lieferscheinid'];
        $orderData['lieferscheinnummer'] = $deliveryNotes[0]['lieferscheinnummer'];

        return $orderData;
    }

    /**
     * Auftrag anhand der Auftragsnummer finden
     *
     * @param string $orderNumber
     *
     * @throws ValidationErrorException
     *
     * @return array
     */
    protected function ensureOrderDataByOrderNumber($orderNumber)
    {
        $order = $this->db->fetchAll(
            'SELECT 
               au.id AS auftragsid, 
               au.projekt AS projektid,
               au.adresse AS adresseid, 
               au.belegnr AS auftragsnummer, 
               au.internet AS internetnummer, 
               au.firma AS firmenid
             FROM auftrag AS au 
             WHERE au.belegnr = :auftragsnummer',
            ['auftragsnummer' => $orderNumber]
        );
        if (count($order) === 0) {
            throw new ValidationErrorException([
                sprintf('Order not found with order number "%s".', $orderNumber),
            ]);
        }
        if (count($order) > 1) {
            throw new ValidationErrorException([
                sprintf('Logic error: Found more than one order with order number "%s".', $orderNumber),
            ]);
        }

        $orderData = $order[0];

        $deliveryNotes = $this->db->fetchAll(
            'SELECT l.id AS lieferscheinid , l.belegnr AS lieferscheinnummer
             FROM lieferschein AS l 
             WHERE l.auftragid = :order_id',
            ['order_id' => $orderData['auftragsid']]
        );
        if (count($deliveryNotes) === 0) {
            throw new ValidationErrorException([
                sprintf('Delivery note not found for order number "%s".', $orderNumber),
            ]);
        }
        if (count($deliveryNotes) > 1) {
            throw new ValidationErrorException([
                sprintf('Logic error: Found more than one delivery note for order number "%s".', $orderNumber),
            ]);
        }

        $orderData['lieferscheinid'] = $deliveryNotes[0]['lieferscheinid'];
        $orderData['lieferscheinnummer'] = $deliveryNotes[0]['lieferscheinnummer'];

        return $orderData;
    }

    /**
     * Auftrag anhand der Lieferscheinnummer finden
     *
     * @param string $deliveryNoteNumber
     *
     * @throws ValidationErrorException
     *
     * @return array
     */
    protected function ensureOrderDataByDeliveryNoteNumber($deliveryNoteNumber)
    {
        $order = $this->db->fetchAll(
            'SELECT 
               au.id AS auftragsid, 
               au.projekt AS projektid,
               au.adresse AS adresseid, 
               au.belegnr AS auftragsnummer, 
               au.internet AS internetnummer, 
               l.belegnr AS lieferscheinnummer,
               l.id AS lieferscheinid,
               au.firma AS firmenid
             FROM lieferschein AS l 
             INNER JOIN auftrag AS au ON l.auftragid = au.id 
             WHERE l.belegnr = :lieferschein',
            ['lieferschein' => $deliveryNoteNumber]
        );
        if (count($order) === 0) {
            throw new ValidationErrorException([
                sprintf('Order not found with delivery note number "%s".', $deliveryNoteNumber),
            ]);
        }
        if (count($order) > 1) {
            throw new ValidationErrorException([
                sprintf('Logic error: Found more than one order with delivery note number "%s".', $deliveryNoteNumber),
            ]);
        }

        return $order[0];
    }

    /**
     * @param string $shippingDate
     *
     * @throws ValidationErrorException
     *
     * @return string
     */
    protected function ensureShippingDateFormat($shippingDate)
    {
        if (!preg_match('#^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$#', $shippingDate)) {
            throw new ValidationErrorException(['Field "versendet_am" does not match required format: YYYY-MM-DD']);
        }

        return $shippingDate;
    }

    /**
     * @param string $parcelCount
     *
     * @throws ValidationErrorException
     *
     * @return int
     */
    protected function ensureParcelCountFormat($parcelCount)
    {
        if (!preg_match('#^[0-9]+$#', $parcelCount)) {
            throw new ValidationErrorException(['Field "anzahlpakete" does not match required format: [0-9]']);
        }

        return (int)$parcelCount;
    }
}
