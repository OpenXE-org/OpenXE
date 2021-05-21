<?php

namespace Xentral\Modules\Api\Controller\Version1;

use Xentral\Components\Http\Response;
use Xentral\Modules\Api\Exception\BadRequestException;
use Xentral\Modules\Api\Exception\ValidationErrorException;

/**
 * Controller zum Anlegen und Bearbeiten von Abo-Artikeln
 *
 * Die Auflistung der Aboartikel-Ressource wird über den GenericController behandelt.
 */
class ArticleSubscriptionController extends AbstractController
{
    /**
     * Abo-Artikel anlegen
     *
     * @return Response
     */
    public function createAction()
    {
        $input = $this->getRequestData();
        $errors = [];

        // Pflichtparameter prüfen
        if (empty($input['bezeichnung'])) {
            $errors[] = 'Required field "bezeichnung" is empty.';
        }
        if (empty($input['artikelnummer']) && empty($input['artikel'])) {
            $errors[] = 'Required fields "artikelnummer" and "artikel" are empty. One of them must be filled.';
        }
        // Artikelnummer in ID wandeln
        if (!empty($input['artikelnummer'])) {
            $input['artikel'] = (int)$this->db->fetchValue(
                'SELECT a.id FROM artikel AS a WHERE a.nummer = :artikelnummer',
                ['artikelnummer' => $input['artikelnummer']]
            );
            // Artikelnummer existiert nicht
            if ($input['artikel'] === 0) {
                $errors[] = 'Artikel not found with article number: ' . $input['artikelnummer'];
            }
            unset($input['artikelnummer']);
        }
        // Kundennummer in Adressen-ID wandeln
        if (!empty($input['kundennummer'])) {
            $input['adresse'] = (int)$this->db->fetchValue(
                'SELECT a.id FROM adresse AS a WHERE a.kundennummer = :kundennummer',
                ['kundennummer' => $input['kundennummer']]
            );
            // Kundennummer existiert nicht
            if ($input['adresse'] === 0) {
                $errors[] = 'Address not found with customer number: ' . $input['kundennummer'];
            }
            unset($input['kundennummer']);
        }

        // Nach Pflichtfeld-Prüfung vorab Fehler anzeigen
        if (count($errors) > 0) {
            throw new ValidationErrorException($errors);
        }

        // Default-Werte hinterlegen
        if (!array_key_exists('startdatum', $input)) {
            $input['startdatum'] = date('Y-m-d');
        }
        if (!array_key_exists('zahlzyklus', $input)) {
            $input['zahlzyklus'] = 1;
        }
        if (!array_key_exists('dokumenttyp', $input)) {
            $input['dokumenttyp'] = 'rechnung';
        }
        if (!array_key_exists('preisart', $input)) {
            $input['preisart'] = 'monat';
        }
        if (!array_key_exists('menge', $input)) {
            $input['menge'] = '0.00';
        }
        if (!array_key_exists('preis', $input)) {
            $input['preis'] = '0.00';
        }
        if (!array_key_exists('rabatt', $input)) {
            $input['rabatt'] = '0.00';
        }
        if (!array_key_exists('waehrung', $input)) {
            $input['waehrung'] = 'EUR';
        }
        if (!array_key_exists('reihenfolge', $input)) {
            $input['reihenfolge'] = 1;
        }
        
        // Aboartikel-Eintrag anlegen
        $resource = $this->getResource($this->resourceClass);
        $result = $resource->insert($input);

        return $this->sendResult($result, Response::HTTP_CREATED);
    }

    /**
     * Abo-Artikel bearbeiten
     *
     * @return Response
     */
    public function updateAction()
    {
        $resource = $this->getResource($this->resourceClass);

        $id = $this->getResourceId();
        $resource->checkOrFail($id);

        $errors = [];
        $input = $this->getRequestData();

        // Artikelnummer in ID wandeln
        if (!empty($input['artikelnummer'])) {
            $input['artikel'] = (int)$this->db->fetchValue(
                'SELECT a.id FROM artikel AS a WHERE a.nummer = :artikelnummer',
                ['artikelnummer' => $input['artikelnummer']]
            );
            // Artikelnummer existiert nicht
            if ($input['artikel'] === 0) {
                $errors[] = 'Artikel not found with article number: ' . $input['artikelnummer'];
            }
            unset($input['artikelnummer']);
        }
        // Kundennummer in Adressen-ID wandeln
        if (!empty($input['kundennummer'])) {
            $input['adresse'] = (int)$this->db->fetchValue(
                'SELECT a.id FROM adresse AS a WHERE a.kundennummer = :kundennummer',
                ['kundennummer' => $input['kundennummer']]
            );
            // Kundennummer existiert nicht
            if ($input['adresse'] === 0) {
                $errors[] = 'Address not found with customer number: ' . $input['kundennummer'];
            }
            unset($input['kundennummer']);
        }

        // Nach Pflichtfeld-Prüfung vorab Fehler anzeigen
        if (count($errors) > 0) {
            throw new ValidationErrorException($errors);
        }
        if (empty($input)) {
            throw new BadRequestException('Payload is empty.');
        }

        $result = $resource->edit($id, $input);

        return $this->sendResult($result);
    }
}
