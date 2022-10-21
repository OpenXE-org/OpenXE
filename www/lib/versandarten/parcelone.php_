<?php

if (!class_exists('Versanddienstleister')) {
    require_once dirname(__DIR__) . '/class.versanddienstleister.php';
}

/**
 * Request & Response Exceptions
 */
interface ParcelOneExceptionInterface
{
}

/**
 * Class ParcelOneReplacer
 *
 * Register some placeholders (with their values) as array
 * and replace each substring of {KEY}, {key}, {{KEY}} or {{key}} with
 * its value.
 */
class ParcelOneReplacer
{
    private $replacements = [];

    /**
     * DPDe_Placeholders constructor.
     *
     * @param array $replacements
     */
    public function __construct($replacements)
    {
        $replacements = array_filter($replacements, static function($e){
            return is_string($e) || is_numeric($e);
        });
        $replacements = array_filter($replacements, 'is_string', ARRAY_FILTER_USE_KEY);
        $replacements = array_change_key_case($replacements, CASE_UPPER);

        $this->replacements = $replacements;
    }

    /**
     * Search and replace variables in string.
     *
     * Replaces all placeholders in upper or lower case within single or double curly brackets
     * @param string $message
     *
     * E.G.: Would convert "hello {customer}." to "hello awesome people."
     * if array to __construct was like ['customer' => 'awesome people']
     *
     * @return string
     */
    public function handle($message)
    {
        if (!is_string($message)) {
            $type = gettype($message);
            throw new RuntimeException(sprintf(
                'Expected message as string, got %s.', $type
            ));
        }

        if (empty($this->replacements)) {
            return $message;
        }

        foreach ($this->replacements as $key => $value) {
            $value = (string) $value;

            // key with one bracket
            $key = '{' . trim($key, '{}') . '}';
            $message = str_replace([$key, strtolower($key)], [$value, $value], $message);
            // key with two brackets
            $key = '{' . $key . '}';
            $message = str_replace([$key, strtolower($key)], [$value, $value], $message);
        }

        return $message;
    }
}

/**
 * This abstract class is designed to simplify a new 'delivery tool'
 *
 * Class AbstractVersandart
 */
abstract class AbstractVersandartParcelone extends Versanddienstleister
{
    public $einstellungen = [];
    public $export_drucker;
    public $paketmarke_drucker;

    /**
     * Copied from other 'versandart modules', added
     * the RuntimeException for json decode.
     *
     * AbstractVersandart constructor.
     *
     * @param app_t $app
     * @param int $id
     */
    final public function __construct($app, $id)
    {
        $this->id = $id;
        $this->app = &$app;
        $settings = $this->app->DB->Select("SELECT einstellungen_json FROM versandarten WHERE id = '$id' LIMIT 1");

        $this->paketmarke_drucker = $this->app->DB->Select("SELECT paketmarke_drucker FROM versandarten WHERE id = '$id' LIMIT 1");
        $this->export_drucker = $this->app->DB->Select("SELECT export_drucker FROM versandarten WHERE id = '$id' LIMIT 1");

        if ($settings) {
            $settings = json_decode($settings, true);
            if (json_last_error()) {
                throw new RuntimeException(sprintf(
                    'JSON decode failed in %s with %s.', get_class($this), json_last_error_msg()
                ));
            }
        } else {
            $settings = [];
        }
        $this->einstellungen = $settings;
        $this->ctrHook();
    }

    /**
     * Just a hook run if the ctr had his job done.
     *
     * @return void
     */
    abstract protected function ctrHook();

    /**
     *
     * Called via 'versandzentrum'
     * Thanks to 'sevensenders.php'
     *
     * @param int|string $id
     * @param $sid
     *
     * @return array
     */
    public function PaketmarkeDrucken($id, $sid)
    {
        $adressdaten = $this->GetAdressdaten($id, $sid);
        $ret = $this->Paketmarke($sid, $id, '', false, $adressdaten);

        if ($sid !== 'lieferschein') {
            return $ret;
        }

        $deliveryNoteArr = $this->app->DB->SelectRow("SELECT adresse, versandart, projekt FROM lieferschein WHERE id = '$id' LIMIT 1");
        $adresse = $deliveryNoteArr['adresse'];
        $project = $deliveryNoteArr['projekt'];
        $versandart = $deliveryNoteArr['versandart'];
        $addressValidation = 2;
        if ($ret) {
            $addressValidation = 1;
        }

        $tracking = null;
        // $tracking = $this->tracking; // $this->tracking is not set in 'sevensenders.php'
        if (isset($adressdaten['tracking'])) {
            $tracking = $adressdaten['tracking'];
        }
        $this->app->DB->Insert("INSERT INTO versand (versandunternehmen, tracking,
              versendet_am,abgeschlossen,lieferschein,freigegeben,firma,adresse,projekt,paketmarkegedruckt,adressvalidation)
              VALUES ('$versandart','$tracking',NOW(),1,'$id',1,'1','$adresse','$project',1,'$addressValidation') ");
        if ($addressValidation === 1) {
            $this->app->erp->LieferscheinProtokoll($id, 'Paketmarke automatisch gedruckt');
        } elseif ($addressValidation === 2) {
            $this->app->erp->LieferscheinProtokoll($id, 'automatisches Paketmarke Drucken fehlgeschlagen');
        }

        return $ret;
    }

    /**
     * This method is the 'main' part of all delivery tools.
     *
     * Available via Lager -> Lieferschein
     * index.php?module=lieferschein&action=paketmarke&id={id}
     * or Lager -> Versandzentrum
     * index.php?module=versanderzeugen&action=frankieren&id={id}
     * or Lager -> Retoure
     * index.php?module=retoure&action=paketmarke&id={id}
     *
     * called via erpapi->Paketmarke($parsetarget,$sid="",$zusatz="",$typ="DHL") as:
     * $error = $obj->Paketmarke($sid!=''?$sid:'lieferschein',($sid=='versand'?$id:$tid), $parsetarget, $error);
     *
     * Reads the address data and package data via '$this->app->Secure->GetPOST' or from '$adressdaten' array.
     *
     * @param string $doctyp 'lieferschein' / 'versand' / 'retoure'
     * @param string|int $id '1'
     * @param string $target '#TAB1'
     * @param bool $error
     * @param null|array $adressdaten
     *
     * @return array
     */
    final public function Paketmarke($doctyp, $id, $target = '', $error = false, &$adressdaten = null)
    {
        if (is_string($id) && is_numeric($id)) {
            $id = (int)$id;
        }
        if (!is_int($id)) {
            $type = gettype($id);
            throw new ArgumentTypeException('Expected id as integer, got ' . $type);
        }
        if (!is_string($doctyp)) {
            $type = gettype($doctyp);
            throw new ArgumentTypeException('Expected doctyp as string, got ' . $type);
        }
        $allowedTypes = ['lieferschein', 'versand', 'retoure'];
        if ($adressdaten === null) {
            $doctyp = $this->getModuleName($doctyp, $allowedTypes);
        }
        if (!in_array($doctyp, $allowedTypes, true)) {
            throw new RuntimeException('Only \'Lieferschein\' is supported, got ' . $doctyp);
        }

        $this->validateSettings();

        if (is_array($adressdaten) && !empty($adressdaten)) {

            $address = [
                'name' => $adressdaten['name'],
                'name2' => $adressdaten['name2'],
                'name3' => $adressdaten['name3'],
                'street' => $adressdaten['strassekomplett'],
                'street_no' => $adressdaten['hausnummer'],
                'plz' => $adressdaten['plz'],
                'ort' => $adressdaten['ort'],
                'email' => $adressdaten['email'],
                'phone' => $adressdaten['phone'],
                'land' => $adressdaten['land'],
                // bundesstaat
            ];

//        $anzahl = (int)isset($adressdaten["anzahl"])?$adressdaten["anzahl"]:0;
//        $nummeraufbeleg = "";//$this->app->Secure->GetPOST("nummeraufbeleg");
//        if ($anzahl <= 0 || !is_int($anzahl)) $anzahl = 1;
//        $laenge = isset($adressdaten["laenge"])?$adressdaten["laenge"]:'';
//        $breite = isset($adressdaten["breite"])?$adressdaten["breite"]:'';
//        $hoehe = isset($adressdaten["hoehe"])?$adressdaten["hoehe"]:'';

            $cash_on_delivery = isset($adressdaten['Nachnahme']) ? $adressdaten['Nachnahme'] : 0;
            $packageData = [
                'kg1' => $adressdaten['standardkg'],
                'drucken' => '1',
                'anders' => '',
                'tracking_again' => '',
                'module' => $doctyp,

                'versandmit' => '', // $this->app->Secure->GetPOST('versandmit'),
                'trackingsubmit' => '', // $this->app->Secure->GetPOST('trackingsubmit'),
                'versandmitbutton' => '', // $this->app->Secure->GetPOST('versandmitbutton'),
                'tracking' => '', // $this->app->Secure->GetPOST('tracking'),
                'trackingsubmitcancel' => '', // $this->app->Secure->GetPOST('trackingsubmitcancel'),
                'retourenlabel' => '', // $this->app->Secure->GetPOST('retourenlabel'),
                'nachnahme' => (int)$cash_on_delivery,
                'product' => '',
            ];

        } else {
            $address = [
                'name' => $this->app->Secure->GetPOST('name'),
                'name2' => $this->app->Secure->GetPOST('name2'),
                'name3' => $this->app->Secure->GetPOST('name3'),
                'street' => $this->app->Secure->GetPOST('strasse'),
                'street_no' => $this->app->Secure->GetPOST('hausnummer'),
                'plz' => $this->app->Secure->GetPOST('plz'),
                'ort' => $this->app->Secure->GetPOST('ort'),
                'email' => $this->app->Secure->GetPOST('email'),
                'phone' => $this->app->Secure->GetPOST('phone'),
                'land' => $this->app->Secure->GetPOST('land'),
            ];
            $packageData = [
                'kg1' => $this->app->Secure->GetPOST('kg1'),
                'drucken' => $this->app->Secure->GetPOST('drucken'),
                'anders' => $this->app->Secure->GetPOST('anders'),
                'tracking_again' => $this->app->Secure->GetGET('tracking_again'),
                'module' => $this->app->Secure->GetGET('module'),
                'versandmit' => $this->app->Secure->GetPOST('versandmit'),
                'trackingsubmit' => $this->app->Secure->GetPOST('trackingsubmit'),
                'versandmitbutton' => $this->app->Secure->GetPOST('versandmitbutton'),
                'tracking' => $this->app->Secure->GetPOST('tracking'),
                'trackingsubmitcancel' => $this->app->Secure->GetPOST('trackingsubmitcancel'),
                'retourenlabel' => $this->app->Secure->GetPOST('retourenlabel'),
                // 'product' => $this->app->Secure->GetPOST('products'),
                'nachnahme' => $this->app->Secure->GetPOST('nachnahme'),
            ];
        }

//        $packageData['clients_reference'] = $this->app->Secure->GetPOST('clients_reference');
//        $packageData['shipment_reference'] = $this->app->Secure->GetPOST('shipment_reference');
        $packageData['service'] = $this->app->Secure->GetPOST('service');

        /*
         * This is in 'sevensenders.php' only called, if $addressdata === null. But why?
         */
        $this->setNachnahmeCheckbox($doctyp, $id);

        $ret = [];
        if (!empty($address)) {
            try {
                // throw new RuntimeException('Ooops - data missing');
                $this->createPaketmarke($doctyp, $id, $target, $error, $address, $packageData);
            } catch (Exception $e) {
                $ret[] = $e->getMessage();
            }
        }

        if ($target) {
            $this->parseTemplate($target);
        }

        return $ret;
    }

    /**
     * Get the module name.
     *
     * @param string $doctype
     * @param array $allowedTypes
     *
     * @return string
     */
    private function getModuleName($doctype, $allowedTypes)
    {
//      // in sevensenders:
//      if($adressdaten === null){
//          $module = $this->app->Secure->GetGET('module');
//      }else{
//          $module = $doctyp;
//      }

        $tmp = $this->app->Secure->GetGET('module');
        if (is_array($tmp)) {
            $tmp = array_filter($tmp);
            $tmp = array_filter($tmp, 'is_string');
            if (array_key_exists(0, $tmp)) {
                $tmp = $tmp[0];
            } else {
                $tmp = '';
            }
        }
        if (in_array($tmp, $allowedTypes, true)) {
            return (string)$tmp;
        }

        return $doctype;
    }

    /**
     * Check if all settings are set and not empty.
     * Uses $this->settingsStructure() to get all
     * required settings. Empty settings are not allowed!
     * Respects select options (drop-down menus). Uses
     * the shown labels in the exceptions.
     *
     * @throws RuntimeException
     */
    protected function validateSettings()
    {
        $settings = $this->EinstellungenStruktur();
        foreach ($settings as $key => $setting) {
            $name = rtrim($setting['bezeichnung'], ':');

            if (!array_key_exists($key, $this->einstellungen)) {
                if (array_key_exists('optional', $setting) && $setting['optional'] === true) {
                    continue;
                }
                if (array_key_exists('typ', $setting) && strtolower((string)$setting['typ']) === 'checkbox') {
                    continue;
                }
                if (array_key_exists('default', $setting)) {
                    $value = $setting['default'];
                    // if default value is empty, don't run other validations.

                    $this->einstellungen[$key] = $value;

                    if (empty($value)) {
                        continue;
                    }
                }
            }

            if (!array_key_exists($key, $this->einstellungen)) {
                throw new RuntimeException(sprintf(
                    'Setting \'%s\' is missing.', $name
                ));
            }

            $value = $this->einstellungen[$key];

            /*
             * Check 'size' argument for maximum length.
             */
            if (array_key_exists('maxLength', $setting) && is_numeric($setting['maxLength'])) {
                $maxLength = (int) $setting['maxLength'];
                if ($maxLength > 0 && strlen($value) > $maxLength) {
                    throw new RuntimeException(sprintf(
                        'Setting \'%s\' raised it\'s maximum length of %s.', $name, $maxLength
                    ));
                }
            }

            /*
             * Respect select fields.
             */
            if (array_key_exists('optionen', $setting) && $setting['type'] === 'select') {
                $options = $setting['optionen'];
                $keys = array_keys($options);

                if (!in_array($value, $keys, true)) {
                    $values = array_values($options);
                    $options = implode(', ', $values);
                    throw new RuntimeException(sprintf(
                        'Setting \'%s\' is not allowed, allowed values are: %s.', $name, $options
                    ));
                }
                continue;
            }

            if (array_key_exists('regex', $setting) && is_string($setting['regex'])) {

                $pattern = $setting['regex'];
                $pattern = trim($pattern, '/');
                $pattern = ltrim($pattern, '^');
                $pattern = rtrim($pattern, '$');
                $pattern = '/^' . $pattern . '$/';
                $match = preg_match($pattern, $value);

                if ($match === false) {
                    throw new RuntimeException(sprintf(
                        'Invalid regex pattern for \'%s\'.', $name
                    ));
                }
                if ($match !== 1) {
                    throw new RuntimeException(sprintf(
                        'Setting \'%s\' does not match it\'s regex pattern.', $name
                    ));
                }
            }
        }
    }

    /**
     * Print the created file.
     *
     * @param string $filename
     * @param string $content
     * @param int $versandId
     *
     * @throws RuntimeException
     */
    protected function printFile($filename, $content, $versandId)
    {
        $printer = $this->getPrinter();
        if (!$printer) {
            throw new RuntimeException('No printer configured.');
        }

        if (!is_string($filename)) {
            $type = gettype($filename);
            throw new RuntimeException(sprintf(
                'Expected filename as string, got %s.', $type
            ));
        }

        if (empty($filename)) {
            throw new RuntimeException(
                'Empty filename is not supported.'
            );
        }

        if (!is_string($content)) {
            $type = gettype($content);
            throw new RuntimeException(sprintf(
                'Expected content as string, got %s.', $type
            ));
        }

        if (empty($content)) {
            throw new RuntimeException(sprintf(
                'Empty content for document %s is not supported.', $filename
            ));
        }

        $tmpPath = $this->app->erp->GetTMP();
        $full = $tmpPath . $filename;

        if (!file_put_contents($full, $content)) {
            throw new RuntimeException(sprintf(
                'Could not write file \'%s\'.', $filename
            ));
        }

        $spoolerId = $this->app->printer->Drucken($printer, $full);
        unlink($full);

        if($versandId && $spoolerId) {
            $this->app->DB->Update(
                sprintf(
                    'UPDATE versand SET lastspooler_id = %d, lastprinter = %d WHERE id = %d',
                    $spoolerId, $printer, $versandId
                )
            );
        }
    }

    /**
     * Return the html structure displayed in the
     * 'versandarten' module to add some extra
     * input fields for api keys etc.
     *
     * Displayed via class 'Versanddienstleister' located
     * in ../class.versanddienstleister.php
     *
     * @return array
     */
    abstract protected function EinstellungenStruktur();

    /**
     * Set the 'nachnahme' checkbox field.
     *
     * Thanks to 'sevensenders.php'
     *
     * @param string $doctyp
     * @param int|string $id
     */
    private function setNachnahmeCheckbox($doctyp, $id)
    {
        if ($doctyp === 'lieferschein') {
            $lieferschein = $id;
        } elseif ($doctyp === 'retoure') {
            $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM retoure WHERE id='$id' LIMIT 1");
        } else {
            $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM versand WHERE id='$id' LIMIT 1");
            if ($lieferschein <= 0) {
                $lieferschein = $id;
            }
        }

        $rechnung = $this->app->DB->Select("SELECT id FROM rechnung WHERE lieferschein='$lieferschein' LIMIT 1");
        $zahlungsweise = $this->app->DB->Select("SELECT zahlungsweise FROM rechnung WHERE id='$rechnung' LIMIT 1");
        if ($zahlungsweise === 'nachnahme') {
            $this->app->Tpl->Set('NACHNAHME', 'checked="checked"');
        }
    }

    /**
     * Create the package labels.
     *
     * Called via Paketmarke or PaketmarkeDrucken
     *
     * @param string $doctyp
     * @param string $id
     * @param string $target
     * @param bool $error
     * @param array $adressdaten
     * @param array $packageData
     *
     * @return array list of error messages
     */
    abstract protected function createPaketmarke($doctyp, $id, $target, $error, $adressdaten, $packageData);

    /**
     * Parse the template.
     *
     * @param string $target
     *
     * @return void
     */
    abstract protected function parseTemplate($target);

    /**
     * May change the tracking id before inserting into the db.
     *
     * @param string $tracking
     *
     * @return string
     */
    public function TrackingReplace($tracking)
    {
        return $tracking;
    }

    /**
     * Extract the child's name and create
     * a default module name via 'ucfirst'.
     *
     * E.G. converts child's class name
     * 'Versandart_parcelone' to 'Parcelone'.
     *
     * @return string
     */
    public function GetBezeichnung()
    {
        $c = get_class($this);
        $c = explode('_', $c);
        $c = array_filter($c);
        $c = array_pop($c);
        $c = ucfirst($c);

        return $c;
    }

    /**
     * Load the data given by the current document type and it's id.
     *
     * @param string $documentTyp on of 'lieferschein', 'versand' or 'retoure'
     * @param int $id
     *
     * @throws RuntimeException
     *
     * @return array
     */
    protected function getDocumentByID($documentTyp, $id)
    {
        /*
         * var $documentTyp may be:
         * - 'lieferschein'
         * - 'versand'
         * - 'retoure'
         *
         * - 'auftrag'
         */
        $documentTyp = $this->app->DB->real_escape_string($documentTyp);
        $sql = sprintf('SELECT * FROM %s WHERE id = %s LIMIT 1', $documentTyp, $id);

        $data = $this->app->DB->SelectArr($sql);
        $error = $this->app->DB->error();

        if ($error) {
            $error = htmlspecialchars($error);
            $msg = sprintf('SQL SelectArr error: \'%s\', query was \'%s\'', $error, $sql);
            throw new RuntimeException($msg);
        }

        if (!is_array($data) || !array_key_exists(0, $data) || !is_array($data[0]) || !$data[0]) {
            throw new RuntimeException(sprintf(
                'No data for document %s with id %s found.', $documentTyp, $id
            ));
        }

        $data = $data[0];

        return $data;
    }

    /**
     * Return the 'standard Paketmarkendrucker'.
     *
     * Checks GetPOST: drucken
     * Checks GetGET: tracking_again
     *
     * @return int
     */
    protected function getPrinter()
    {
        if (is_numeric($this->paketmarke_drucker) && $this->paketmarke_drucker) {
            return $this->paketmarke_drucker;
        }

        $printer = (int) $this->app->erp->GetStandardPaketmarkendrucker();
        if ($printer) {
            return $printer;
        }

        return $this->export_drucker;
    }

    /**
     * Get the current user name as escaped string.
     *
     * @return string
     */
    protected function getUserName()
    {
        $user = $this->app->User->GetName();
        $user = $this->app->DB->real_escape_string($user);

        return $user;
    }

    /**
     * Load an address via it's id.
     *
     * @param string|int $id
     * @param string|array $fields Filter these columns, default all
     *
     * @throws RuntimeException
     *
     * @return array
     */
    protected function loadAddress($id, $fields = '*')
    {
        if (is_string($id) && is_numeric($id)) {
            $id = (int)$id;
        }
        if (!is_int($id)) {
            $type = gettype($id);
            throw new ArgumentTypeException('Expected addressID as int, got ' . $type);
        }

        $fields = (array)$fields;
        $fields = array_values($fields);
        $fields = array_filter($fields);
        $fields = array_filter($fields, 'is_string');
        if (in_array('*', $fields, true)) {
            $fields = '*';
        } else {
            $tmp = [];
            foreach ($fields as $field) {
                $tmp[] = $this->app->DB->real_escape_string($field);
            }
            $fields = implode(', ', $tmp);
        }

        $sql = sprintf('SELECT %s FROM adresse WHERE id =\'%s\'', $fields, $id);
        $query = $this->app->DB->Query($sql);
        $address = $query->fetch_array(MYSQLI_ASSOC);
        $error = $this->app->DB->error();

        if ($error) {
            $error = htmlspecialchars($error);
            $msg = sprintf('SQL query error: \'%s\', query was \'%s\'', $error, $sql);
            throw new RuntimeException($msg);
        }

        if (!is_array($address)) {
            $type = gettype($address);
            throw new ArgumentTypeException('Expected address as array, got ' . $type);
        }

        return $address;
    }

    /**
     * Get the package weight from user input.
     *
     * @param array $packageData
     *
     * @throws RuntimeException
     *
     * @return float
     */
    protected function getWeight($packageData)
    {
        if (!is_array($packageData)) {
            $type = gettype($packageData);
            throw new ArgumentTypeException('Expected package data as array, got ' . $type);
        }
        if (!array_key_exists('kg1', $packageData)) {
            throw new RuntimeException('Weight (kg1) is missing.');
        }

        $weight = $packageData['kg1'];
        if (empty($weight) && $weight !== '0') {
            // wtf?! why is '0' string empty?
            throw new RuntimeException('The package weight is required.');
        }
        if (is_string($weight)) {
            $weight = str_replace(',', '.', $weight);
            if (is_numeric($weight)) {
                $weight = (float)$weight;
            }
        }
        if (!is_float($weight)) {
            $type = gettype($weight);
            throw new ArgumentTypeException('Expected weight as float, got ' . $type);
        }
        if ($weight < 0) {
            throw new RuntimeException('A negative package weight is not supported.');
        }

        return $weight;
    }

    /**
     * Build an html select element.
     *
     * @param string $nameID Name and ID of select element.
     * @param array $select associative array
     * @param string|int|null|array $selected
     *
     * @return string
     */
    protected function buildSelectForm($nameID, $select, $selected = null)
    {
        $options = [];
        $nameID = htmlspecialchars($nameID);

        $options[] = sprintf('<select id="%s" name="%s" style="width:23em">', $nameID, $nameID);
        foreach ($select as $key => $value) {
            $key = htmlspecialchars($key);
            $value = htmlspecialchars($value);

            $mark = $selected === $key
                ? 'selected="selected"'
                : '';

            $options[] = sprintf('<option value="%s" %s>%s</option>', $key, $mark, $value);
        }
        $options[] = '</select>';

        return implode('', $options);
    }
}

class ArgumentTypeException extends RuntimeException implements ParcelOneExceptionInterface
{
}

class ResponseException extends RuntimeException implements ParcelOneExceptionInterface
{
}

class NotImplementedException extends RuntimeException implements ParcelOneExceptionInterface
{
}

class EmptyResponseException extends ResponseException implements ParcelOneExceptionInterface
{
}

class MissingArgumentException extends RuntimeException implements ParcelOneExceptionInterface
{
}

class MissingResponseFieldException extends ResponseException implements ParcelOneExceptionInterface
{
    /**
     * MissingResponseFieldException constructor.
     *
     * Change the exception message -> wrap it, so its enough to pass only the missed argument name.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     *
     * @return static
     */
    final public static function fromFieldName($message, $code = 0, Throwable $previous = null)
    {
        if (!$message || !is_string($message)) {
            $message = 'A field is missing.';
        }
        $message = sprintf('The field %s is missing.', $message);

        return new static($message, $code, $previous);
    }
}

class SoapExtensionMissingException extends RuntimeException implements ParcelOneExceptionInterface
{
}

if (!class_exists('SoapHeader')) {
    /**
     * This is just a fix, if php comes without the soap extension.
     *
     * So the following header classes can extend this class without raising an unhandled exception just by loading this
     * file. A check if the soap extension exists is located in Versandart_parcelone::EinstellungenStruktur() so the
     * user cannot setup this delivery service. In addition, a check is located in
     * AbstractParcelOneRequest::__construct() so it's not possible to execute a soap request if the extension is
     * missing.
     *
     * Class SoapHeader
     */
    class SoapHeader
    {
        public function __construct($namespace, $name, $data = null, $mustunderstand = false, $actor = '')
        {
        }
    }
}
/**
 * Define some constants if the soap extension is not available so at least the class instantiation runs as expected.
 *
 * @url: https://www.php.net/manual/en/soap.constants.php
 */
defined('SOAP_1_1') or define('SOAP_1_1', 1);
defined('WSDL_CACHE_NONE') or define('WSDL_CACHE_NONE', 0);
defined('SOAP_COMPRESSION_GZIP') or define('SOAP_COMPRESSION_GZIP', 0);
defined('SOAP_COMPRESSION_ACCEPT') or define('SOAP_COMPRESSION_ACCEPT', 32);

/**
 * Headers from documentation package.
 *
 * classes:
 * - AuthHeader
 * - APIKeyHeader
 * - CultureHeader
 *
 * Thanks to Jörk Sternsdorff from
 * Awiwe Solutions GmbH
 */
class AuthHeader extends SoapHeader
{
    private $wss_ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
    private $wsu_ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';

    /**
     * AuthHeader constructor.
     *
     * @param string $user
     * @param string $pass
     */
    public function __construct($user, $pass)
    {
        $created = gmdate('Y-m-d\TH:i:s\Z');
        $nonce = mt_rand();
        $passdigest = base64_encode(pack('H*', sha1(pack('H*', $nonce) . pack('a*', $created) . pack('a*', $pass))));

        $auth = new stdClass();
        $auth->Username = new SoapVar($user, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
        $auth->Password = new SoapVar($pass, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
        $auth->Nonce = new SoapVar($passdigest, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
        $auth->Created = new SoapVar($created, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wsu_ns);

        $username_token = new stdClass();
        $username_token->UsernameToken = new SoapVar($auth, SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'UsernameToken', $this->wss_ns);

        $security_sv = new SoapVar(
            new SoapVar($username_token, SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'UsernameToken', $this->wss_ns),
            SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'Security', $this->wss_ns);
        parent::__construct($this->wss_ns, 'Security', $security_sv, true);
    }
}

class APIKeyHeader extends SoapHeader
{
    public function __construct($apiKey)
    {
        parent::__construct('apikey', 'apikey', $apiKey, false);
    }
}

class CultureHeader extends SoapHeader
{
    public function __construct($culture)
    {
        parent::__construct('culture', 'culture', $culture, false);
    }
}

/**
 * Class AbstractParcelOneRequest
 *
 * This class holds only some functionality to
 * build up the soap request. It supports some
 * methods to set required header.
 * Nothing more.
 *
 * @package ParcelOne
 */
abstract class AbstractParcelOneRequest
{
    /*
     * Support only PA1 as carrier.
     */
    const DEFAULT_CARRIER = 'PA1';

    /**
     * this Key is used to identify this software and not the customer.
     */
    const API_KEY = '0641C551-D23A-43BB-87A9-626DAE7FFE00';

    /**
     * The default software attribute is injected into every request.
     * It's possible to 'override' this constant in child classes.
     */
    const SOFTWARE = 'Xentral_ERP_Software';

    const SERVICE = 'https://productionapi.awiwe.solutions/version4/shippingwcf/ShippingWCF.svc?wsdl';
    const SANDBOX_SERVICE = 'https://sandboxapi.awiwe.solutions/version4/shippingwcfsandbox/shippingWCF.svc?wsdl';

    const ENDPOINT = 'https://productionapi.awiwe.solutions/version4/shippingwcf/ShippingWCF.svc/Shippingwcf';
    const SANDBOX_ENDPOINT = 'https://sandboxapi.awiwe.solutions/version4/shippingwcfsandbox/shippingWCF.svc/ShippingWCF';
    /**
     * @var AbstractParcelOneRequest|ParcelOneRequest
     */
    static private $instance = null;
    /**
     * The Mandator ID
     *
     * ID at PARCEL.ONE, usually '1', if only one mandator.
     *
     * @var int 1
     */
    protected $mandator = 1;
    /**
     * The Consigner ID
     *
     * ID at PARCEL.ONE, usually "1", if only one consigner.
     *
     * @var int
     */
    protected $consigner = 1;
    /**
     * The carrier selected as second step in the settings.
     *
     * @var string default PA1 for Parcel.One
     */
    protected $carrier = 'PA1';
    /**
     * @var string The product chosen in the settings.
     */
    protected $product = '';

    /**
     * Options for the SoapClient constructor.
     *
     * Note: this property is private. Also child classes have to use
     * the setOption / deleteOption methods.
     *
     * @var array
     */
    private $options = [
        'soap_version' => SOAP_1_1,
        'exceptions' => true,
        'trace' => false,
        'cache_wsdl' => WSDL_CACHE_NONE,
        'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
        'location' => 'https://shippingwcf.awiwe.net/Shippingwcf.svc/Shippingwcf',
        'connection_timeout' => 300
    ];
    /**
     * @var array of RequestHeaderInterface
     */
    private $soapHeaders = [];
    /**
     * @var string
     */
    private $url;
    /**
     * Cache here the created soap client.
     *
     * @var SoapClient
     */
    private $client = null;

    /**
     * ParcelOneRequest constructor.
     *
     * @param bool $production
     * @param int $mandator
     * @param int $consigner
     */
    final private function __construct($production = false, $mandator = 1, $consigner = 1)
    {
        if (!class_exists('SoapClient')) {
            throw new SoapExtensionMissingException('SOAP support is not configured.');
        }

        $this->mandator = $mandator;
        $this->consigner = $consigner;

        $this->url = self::SANDBOX_SERVICE;
        $this->options['location'] = self::SANDBOX_ENDPOINT;
        if ($production) {
            $this->url = self::SERVICE;
          $this->options['location'] = self::ENDPOINT;
        }
    }

    /**
     * Get the singleton.
     *
     * @param array $settings
     * @return AbstractParcelOneRequest|ParcelOneRequest
     */
    final public static function getInstance($settings = [])
    {
        if (self::$instance !== null) {
            if (empty(self::$instance->carrier) && array_key_exists('carrier', $settings)) {
                self::$instance->carrier = $settings['carrier'];
            }
            if (empty(self::$instance->product) && array_key_exists('product', $settings)) {
                self::$instance->product = $settings['product'];
            }
            return self::$instance;
        }

        $required = ['kdnr', 'password', 'sandbox'];
        foreach ($required as $requirenment) {
            if (!array_key_exists($requirenment, $settings)) {
                throw new MissingArgumentException(sprintf(
                    'Setting %s is missing.', $requirenment
                ));
            }
        }
        $required = array_flip($required);
        $settings = array_intersect_key($settings, $required);

        $production = true;
        if (array_key_exists('sandbox', $settings)) {
            $production = $settings['sandbox'] !== '1';
        }
        if (!array_key_exists('mandator', $settings)) {
            $settings['mandator'] = 1;
        }
        $mandator = (int)$settings['mandator'];
        $mandator = max(1, $mandator);

        if (!array_key_exists('consigner', $settings)) {
            $settings['consigner'] = 1;
        }
        $consigner = (int)$settings['consigner'];
        $consigner = max(1, $consigner);

        if (!array_key_exists('country', $settings)) {
            $settings['country'] = 'de-DE';
        }

        $instance = new static($production, $mandator, $consigner);
        $instance
            ->addSoapHeader(new AuthHeader($settings['kdnr'], $settings['password']))
            ->addSoapHeader(new CultureHeader($settings['country']))
            ->addSoapHeader(new APIKeyHeader(self::API_KEY));

        self::$instance = $instance;

        return $instance;
    }

    /**
     * Don't allow 'on the fly' calls.
     *
     * Only the implemented SOAP methods are supported.
     *
     * @param string $name
     * @param array $arguments
     *
     * @throws NotImplementedException
     *
     * @return mixed
     */
    final public function __call($name, $arguments)
    {
        $class = get_class($this);
        $message = sprintf(
            'Method %s->%s is not implemented.', $class, $name
        );

        throw new NotImplementedException($message);
    }

    /**
     * Make one call to the PARCEL.ONE API
     *
     * @param string $name
     * @param array $arguments
     *
     * @throws SoapFault
     *
     * @throws EmptyResponseException
     * @return stdClass|mixed
     */
    final protected function call($name, $arguments = null)
    {
        $client = $this->getClient();

        if (!is_string($name)) {
            $type = gettype($name);
            throw new InvalidArgumentException(sprintf(
                'Expected method name as string, got %s.', $type
            ));
        }

        if ($arguments === null) {
            $arguments = [];
        }
        if (!is_array($arguments)) {
            $type = gettype($arguments);
            throw new InvalidArgumentException(sprintf(
                'Expected %s\'s arguments as array, got %s.', $name, $type
            ));
        }

        $arguments['Software'] = self::SOFTWARE;

        /*
         * Do some magic ;)
         */
        $callback = [$client, $name];

        // $response = call_user_func($callback, $arguments);
        $response = $callback($arguments);

        if (empty($response)) {
            $message = sprintf('Method %s returned empty body.', $name);
            throw new EmptyResponseException($message);
        }

        return $response;
    }

    /**
     * Return the SOAP client.
     *
     * Set it up, if not available now.
     *
     * @throws SoapFault
     *
     * @return SoapClient
     */
    final protected function getClient()
    {
        $client = $this->client;
        if ($client === null) {
            $client = new SoapClient($this->url, $this->options);
            $client->__setSoapHeaders($this->soapHeaders);
            $this->client = $client;
        }

        return $this->client;
    }

    /**
     * Convert the stdClass object into an array.
     *
     * This method uses recursive calls.
     *
     * Thanks to https://stackoverflow.com/a/18576919
     *
     * @param stdClass|array $array
     *
     * @return array
     */
    final protected function convert2array($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $array[$key] = $this->convert2array($value);
                }
                if ($value instanceof stdClass) {
                    $array[$key] = $this->convert2array((array)$value);
                }
            }
        }
        if ($array instanceof stdClass) {
            return $this->convert2array((array)$array);
        }
        return $array;
    }

    /**
     * @param SoapHeader $header
     *
     * @return $this
     */
    private function addSoapHeader(SoapHeader $header)
    {
        $this->client = null;
        $this->soapHeaders[] = $header;
        return $this;
    }
}

/**
 * Class ParcelOneRequest
 *
 * Implement here all API methods we support.
 */
class ParcelOneRequest extends AbstractParcelOneRequest
{
    /**
     * Get available Products for a mandator and Carrier
     *
     * @param int|string $level : [0, 1, 2, ..]
     * - 0 = all levels returned;
     * - 1 = only 1 level returned (only products),
     * - >=2 = 2 levels returned (products and services info)
     *
     * @throws SoapFault
     *
     * @return array|stdClass
     */
    public function getProducts($level = 1)
    {
        $level = is_numeric($level) ? $level : 1;
        $level = max(0, (int)$level);

        $arguments = [
            'Mandator' => $this->mandator, // string, as field in settings?
            'level' => $level, // int
            'CEP' => $this->carrier, // string Carrier abbreviation (UPS, PA1 for Parcel One, ...).
        ];

        $response = $this->call(__FUNCTION__, $arguments);

        if (!isset($response->getProductsResult)) {
            throw MissingResponseFieldException::fromFieldName('getProductsResult');
        }
        if (!isset($response->getProductsResult->Product)) {
            throw MissingResponseFieldException::fromFieldName('getProductsResult->Product');
        }
        $response = $response->getProductsResult->Product;
        $response = $this->convert2array($response);

        $products = [];
        // get as first the default product
        foreach ($response as $product) {
            if (array_key_exists('Default', $product) && $product['Default']) {
                $id = $product['ProductID'];
                $name = $product['ProductName'];

                $products[$id] = $name;
            }
        }
        // then add all other products
        foreach ($response as $product) {
            if (array_key_exists('Default', $product) && !$product['Default']) {
                $id = $product['ProductID'];
                $name = $product['ProductName'];

                $products[$id] = $name;
            }
        }

        return $products;
    }

    /**
     * Register Forward and Return Shipments.
     *
     * @param $shippingData
     *
     * @throws SoapFault
     *
     * @return array
     */
    public function registerShipments($shippingData)
    {
        $default = [
            // MandatorID: required - Mandator ID at PARCEL.ONE, usually "1", if only one mandator.
            'MandatorID' => $this->mandator,
            // ConsignerID: required - Consigner ID at PARCEL.ONE, usually "1", if only one consigner.
            'ConsignerID' => $this->consigner,
            // CEPID: required - Carrier specification, possible values so far: UPS, PA1, DHL.
            'CEPID' => $this->carrier,
            // Software: required - Software specification of client, if possible with version number.
            'Software' => self::SOFTWARE,
            /*
             * PrintDocuments: required - Flag to indicate,
             * if Documents (for DHL also Export Documents) should be returned with this request
             * (0=no, 1=yes).
             */
            'PrintDocuments' => 1,
            // DocumentFormat: required - only Format.Type = "PDF" needed.
            'DocumentFormat' => ['Type' => 'PDF'],
            // LabelFormat: required - only Format.Type = either "GIF" or "PDF" needed.
            'LabelFormat' => ['Type' => 'PDF'],
            // PrintLabel: required - Flag to indicate, if Label should be returned with this request (0=no, 1=yes).
            'PrintLabel' => 1,
        ];

        $shippingData = array_merge($shippingData, $default);
        $shippingData = ['ShippingData' => [$shippingData]];
        $response = $this->call(__FUNCTION__, $shippingData);

        if (!isset($response->registerShipmentsResult)) {
            throw MissingResponseFieldException::fromFieldName('registerShipmentsResult');
        }

        $response = $this->convert2array($response);

        if (!isset($response['registerShipmentsResult'])) {
            throw new ResponseException('Missing registerShipmentsResult response field.');
        }
        $response = $response['registerShipmentsResult'];

        if (!isset($response['ShipmentResult'])) {
            throw new ResponseException('Missing ShipmentResult response field.');
        }
        $response = $response['ShipmentResult'];

        foreach ($response['ActionResult']['Errors'] as $e) {
            if (!is_array($e)) {
                continue;
            }
            if (!array_key_exists('Message', $e) || !$e['Message']) {
                continue;
            }
            $msg = $e['Message'];
            if (array_key_exists('StatusCode', $e) && $e['StatusCode']) {
                $msg .= ' (' . (string)$e['StatusCode'] . ')';
            }
            throw new ResponseException($msg);
        }

        return $response;
    }

    /**
     * Get available Carriers for a mandator, optionally filtered by countries list.
     *
     * CEP[] getCEPs(string Mandator, int level, String[] Countries);
     *
     * @param int|string level: [0, 1, 2, ..]
     *  0 = all levels returned;
     *  1 = only 1 level returned (only products),
     *  >=2 = 2 levels returned (products and services info)
     *
     * @throws SoapFault
     *
     * @return array|stdClass
     */
    public function getCEPs($level = 1)
    {
        $arguments = [
            'Mandator' => $this->mandator,
            'level' => max(1, (int) $level),
        ];

        $response = $this->call(__FUNCTION__, $arguments);

        if (!isset($response->getCEPsResult)) {
            throw new ResponseException('Missing getCEPsResult field.');
        }

        if (!isset($response->getCEPsResult->CEP)) {
            throw new ResponseException('Missing CEP field.');
        }

        $response = $response->getCEPsResult->CEP;
        $response = $this->convert2array($response);

        return $response;
    }

    /**
     * @throws SoapFault
     */
    public function getServices()
    {
        $arguments = [
            'Mandator' => $this->mandator,
            'CEP' => $this->carrier,
            'Product' => $this->product,
        ];

        $response = $this->call(__FUNCTION__, $arguments);

        if (!isset($response->getServicesResult)) {
            throw MissingResponseFieldException::fromFieldName('getServicesResult');
        }
        if (!isset($response->getServicesResult->Service)) {
            throw MissingResponseFieldException::fromFieldName('getServicesResult::Service.');
        }

        $response = $response->getServicesResult->Service;
        $response = $this->convert2array($response);

        $services = [
            '' => 'Kein Service'
        ];
        // Search for the default service
        foreach ($response as $service) {
            if (array_key_exists('Default', $service) && $service['Default']) {
                $id = $service['ServiceID'];
                $name = $service['ServiceName'];

                $services[$id] = $name;
            }
        }
        // Append all other services
        foreach ($response as $service) {
            if (array_key_exists('Default', $service) && ! $service['Default']) {
                $id = $service['ServiceID'];
                $name = $service['ServiceName'];

                $services[$id] = $name;
            }
        }

        return $services;
    }

    // todo: implement here the necessary methods
}

/**
 * API from PARCEL.ONE
 *
 * Note: the class name is expected as:
 *
 * Versandart_{filename}
 *
 * Where {filename} is lowercase and without the '.php' extension.
 *
 * @url: https://parcel.one/en
 * @url: https://parcel.one/en/api
 */
class Versandart_parcelone extends AbstractVersandartParcelone
{
    /**
     * @inheritDoc
     */
    public function GetBezeichnung()
    {
        return 'PARCEL.ONE';
    }

    protected function ctrHook()
    {
        $current = [
            'kdnr' => $this->app->Secure->GetPOST('kdnr'),
            'password' => $this->app->Secure->GetPOST('password'),
            'carrier_product' => $this->app->Secure->GetPOST('carrier_product'),
        ];
        $current = array_filter($current);
        // $this->einstellungen += $current;
        $this->einstellungen = array_merge($this->einstellungen, $current);

        if (array_key_exists('carrier_product', $this->einstellungen)) {
            list($carrier, $product) = explode('.', $this->einstellungen['carrier_product']);
            $this->einstellungen['carrier'] = $carrier;
            $this->einstellungen['product'] = $product;
        }
    }

    /**
     * @inheritDoc
     */
    protected function EinstellungenStruktur()
    {
        if (!class_exists('SoapClient')) {
            $message = 'PHP SOAP Extension ist nicht konfiguriert. Diese Versandart kann nicht genutzt werden.';
            $this->app->Tpl->Add('MESSAGE', sprintf('<div class="error">%s</div>', $message));

            return [];
        }

        $select = [];
        $settings = array_filter($this->einstellungen);
//        $this->app->Tpl->Add('MESSAGE', '<div class="error">' . json_encode($this->einstellungen) . '</div>');
//        $this->app->Tpl->Add('MESSAGE', '<div class="error">' . json_encode($settings) . '</div>');

        try {
            if (!empty($settings)) {
                $carriers = [];
                if (!array_key_exists('kdnr', $settings) || !array_key_exists('password', $settings)) {
                    $this->app->Tpl->Add('MESSAGE', '<div class="error">' . 'Bitte gültige API-Zugangsdaten angeben' . '</div>');
                } else {
                    $request = ParcelOneRequest::getInstance($this->einstellungen);
                    $carriers = $request->getCEPs(2);
                    if (!array_key_exists('carrier_product', $settings)) {
                        $this->app->Tpl->Add('MESSAGE', '<div class="info">API-Key erfolgreich überprüft</div>');
                    }
                }

                if (!array_key_exists(0, $carriers)) {
                    // i'm not sure about the structure if multiple carriers are available.
                    $tmp = $carriers;
                    $carriers = [];
                    $carriers[] = $tmp;
                    unset($tmp);
                }

                foreach ($carriers as $carrier) {
                    $cepID = $carrier['CEPID'];
                    $cepName = $carrier['CEPLongname'];

                    foreach ($carrier['Products']['Product'] as $product) {
                        $productID = $product['ProductID'];
                        $productName = $product['ProductName'];

                        $select[$cepID . '.' . $productID] = $cepName . ': ' . $productName;
                    }
                }
            }
        }catch (Exception $e) {
            $this->app->Tpl->Add('MESSAGE', '<div class="error">' . $e->getMessage() . '</div>');
            $select = [];
        }

        if (empty($select)) {
            $select = ['Bitte Zugangsdaten berichtigen'];
        } else if (!array_key_exists('carrier_product', $settings)) {
            $this->app->Tpl->Add('MESSAGE', '<div class="info">Bitte ein Produkt wählen</div>');
        } else {
            $tmp = [];
            $firstID = $settings['carrier_product'];
            // add the chosen one as first element to $tmp
            foreach ($select as $id => $name) {
                if ($id === $firstID) {
                    $tmp[$id] = $name;
                }
            }
            // append all other elements
            foreach ($select as $id => $name) {
                if ($id !== $firstID) {
                    $tmp[$id] = $name;
                }
            }
            $select = $tmp;
            unset($tmp);
        }

        return [
            'kdnr' => [
                'typ' => 'text',
                'bezeichnung' => 'Kundennummer:',
                'size' => 40,
            ],
            'password' => [
                'typ' => 'text',
                'bezeichnung' => 'Passwort:',
                'size' => 40,
            ],
            'country' => [
                'typ' => 'text',
                'bezeichnung' => 'Absender Land:',
                'size' => 2,
                'placeholder' => 'DE',
                'default' => 'DE',
                'regex' => '^[A-Z]{2}$'
            ],
            'international' => [
                'size' => 40,
                'typ' => 'select',
                'default' => 'CN23',
                'bezeichnung' => 'Zolldokumente:',
                'optionen' => [
                    '' => 'Nicht benötigt',
                    'CN22' => 'CN22',
                    'CN23' => 'CN23',
                ],
            ],
            'ref1' => [
                'size' => 40,
                'typ' => 'text',
                'default' => '',
                'bezeichnung' => 'Referenz 1:',
                'placeholder' => 'Referenz 1 auf Label',
                'info'=>'{LIEFERSCHEIN}, {AUFTRAG}, {PROJEKT}, {IHREBESTELLNUMMER}, {INTERNET}',
            ],
            'ref2' => [
                'size' => 40,
                'typ' => 'text',
                'default' => '',
                'bezeichnung' => 'Referenz 2:',
                'placeholder' => 'Referenz 2 auf Label',
                'info'=>'{LIEFERSCHEIN}, {AUFTRAG}, {PROJEKT}, {IHREBESTELLNUMMER}, {INTERNET}',
            ],
            'carrier_product' => [
                'size' => 40,
                'typ' => 'select',
                'bezeichnung' => 'Spediteur & Produkt:',
                'optionen' => $select,
            ],
          'standardgewicht' => [
            'size' => 40,
            'typ' => 'text',
            'bezeichnung' => 'Standardgewicht'
          ],
            'autotracking' => [
                'typ' => 'checkbox',
                'bezeichnung' => 'Tracking übernehmen:'
            ],
          'sandbox' => [
            'typ' => 'checkbox',
            'bezeichnung' => 'Sandbox Anbindung:'
          ],
        ];
    }

    /**
     * @inheritDoc
     *
     * @throws SoapFault
     */
    protected function parseTemplate($target)
    {
        if (!array_key_exists('carrier', $this->einstellungen)) {
            $this->app->Tpl->Add('MESSAGE', '<div class="error">Bitte die Einstellungen vervollständigen.</div>');
        } else {
            $request = ParcelOneRequest::getInstance($this->einstellungen);

            $services = $request->getServices();
            unset($services['']);

            $html = '';
            $frame = '<div><input type="checkbox" id="service_%s" name="service[]" value="%s"><label for="service_%s">%s</label></div>';
            foreach ($services as $id => $service) {
                $html .= sprintf($frame, $id, $id, $id, $service);
            }
            $services = $html;

            $this->app->Tpl->Set('SERVICE', $services);
        }
        $this->app->Tpl->Parse($target, 'versandarten_parcelone.tpl');
    }

    public function VersandartMindestgewicht()
    {
      if(!empty($this->einstellungen['standardgewicht'])){
        return str_replace(',','.',$this->einstellungen['standardgewicht']);
      }
      return 0;
    }

    /**
     * @inheritDoc
     *
     * @throws SoapFault
     */
    protected function createPaketmarke($doctyp, $id, $target, $error, $adressdaten, $packageData)
    {
        $versandId = $doctyp==='versand'?$id:0;
        if (!array_key_exists('carrier', $this->einstellungen)) {
            $this->app->Tpl->Add('MESSAGE', '<div class="error">Bitte die Einstellungen vervollständigen.</div>');
            return false;
        }

        // lieferschein, retoure & versand contain all an 'adresse' field.
        $document = $this->getDocumentByID($doctyp, $id);
        if (!array_key_exists('adresse', $document)) {
            throw new MissingArgumentException(sprintf(
                'Missed field for adresse in %s.', $doctyp
            ));
        }

        $lieferscheinID = $id;
        if (array_key_exists('lieferscheinid', $document)) {
          $lieferscheinID = $document['lieferscheinid'];
        }

        $address = $this->loadAddress($document['adresse']);
        $items = $this->loadDeliveryPositions($id, $doctyp);

        $auftragnummer = $this->getAuftragNummer($lieferscheinID);
        $projektabkuerzung = $this->getProjectShortName($lieferscheinID);
        // Source: ups.php
        $ihrebestellnummer = $this->app->DB->Select("SELECT ihrebestellnummer FROM lieferschein WHERE id='$lieferscheinID' LIMIT 1");
        $lieferscheinnummer = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$lieferscheinID' LIMIT 1");
        $internet = $this->app->DB->Select("SELECT a.internet FROM lieferschein l LEFT JOIN auftrag a ON a.id=l.auftragid WHERE l.id='$lieferscheinID' LIMIT 1");

        $replacer = new ParcelOneReplacer([
            'IHREBESTELLNUMMER' => $ihrebestellnummer,
            'LIEFERSCHEIN' => $lieferscheinnummer,
            'PROJEKT' => $projektabkuerzung,
            'AUFTRAG' => $auftragnummer,
            'INTERNET' => $internet
        ]);

        /*
         * ShipTo Reference provided by client, string, max length 20
         */
        $clientsReference = $this->einstellungen['ref2'];
        $clientsReference = $replacer->handle((string) $clientsReference);
        $clientsReference = substr($clientsReference, 0, 20);

        /*
         * Private Address = 1, B2B = 0
         */
        $private = $address['firma'] !== '1' ? 1 : 0;
        /*
         * ReturnShipmentIndicator: required -
         * 0 = Forward Shipment,
         * all values > 0 = Return Shipment.
         *
         * For UPS the following are available:
         * 2-Print and Mail Return Label by UPS;
         * 3-Return Service 1-Attempt;
         * 5-Return Service 3-Attempt;
         * 8-Electronic Return Label by URL;
         * 9-Print Return Label.
         *
         * For DHL and Parcel One so far not available.
         *
         * We only support Parcel One, so:
         * 0 -> forward,
         * 1 -> return
         */
        $returnShipment = (int)$doctyp === 'retoure';
        /*
         * Parcel ID assigned at Shipping.
         * will be reassigned at successfully shipping.
         */
        $packageID = '';
        /*
         * Shipment Reference field provided by client for identification, string, max length 20.
         */
        $shipmentRef = $this->einstellungen['ref1']; // $packageData['shipment_reference'];
        $shipmentRef = $replacer->handle((string) $shipmentRef);
        $shipmentRef = substr($shipmentRef, 0, 20);

        /*
         * Certificate No to print on CN23.
         */
        $certificateNo = '';
        /*
         * optional - Invoice No to print on CN23. max. length: 20
         * $lieferschein['belegnummer'] // 'ihrebestellnummer'
         */
        $invoiceNo = '';
        /*
         * optional item category
         * possible values:
         * - 1: Gift
         * - 2: Documents
         * - 3: Commercial Sample
         * - 4: Returned Goods
         * - 5: Other
         */
        $itemCategory = $returnShipment ? 4 : 5;

        $weight = $this->getWeight($packageData);
        if (!$weight) {
            foreach ($items as $item) {
                $weight += (float) $item['NetWeight'];
            }
        }
        $weight = number_format($weight, 3);

        $product = $this->einstellungen['product'];
        $international = $this->getInternationalDocumentType($adressdaten['land']);

        $destinationCountry = $adressdaten['land'];
        $originCountry = $this->einstellungen['country'];
        $printInternationalDocuments = 0;
        if($destinationCountry != $originCountry){
          $printInternationalDocuments = (int)(!$this->app->erp->IsEU($destinationCountry));
        }

        $shipment = [
            'ProductID' => $product,
            'ShipmentRef' => $shipmentRef,
            'ReturnShipmentIndicator' => $returnShipment,
            'ShipToData' => [
                'Name1' => $adressdaten['name'],
                'Name2' => $adressdaten['name2'],
                'Name3' => $adressdaten['name3'],
                /*
                 * PrivateAddressIndicator: optional/required
                 * - 1 Private Address,
                 * - 0 B2B-Address.
                 */
                'PrivateAddressIndicator' => $private,
                'ShipmentAddress' => [
                    'City' => $adressdaten['ort'],
                    'PostalCode' => $adressdaten['plz'],
                    'Street' => $adressdaten['street'],
                    'Streetno' => $adressdaten['street_no'],
                    'Country' => $adressdaten['land'],
//                    'State' => '',
//                    'District' => '',
                ],
                'Reference' => $clientsReference,
            ],
            'Packages' => [
                [
                    'PackageWeight' => [
                        'Value' => $weight,
                    ],
                    'PackageID' => $packageID,
                    'IntDocData' => [
                        'ContentsDesc' => $items,
                        'ShipToRef' => $shipmentRef,
                        'ItemCategory' => $itemCategory,
                        'InvoiceNo' => (string)$invoiceNo,
                        'Invoice' => (int)(bool)$invoiceNo,
                        'PrintInternationalDocuments' => (int) ! empty($international),
                        'Explanation' => $clientsReference,
                        'ConsignerCustomsID' => '',
                        'CertificateNo' => (string)$certificateNo,
                        'Certificate' => (int)(bool)$certificateNo,
                        'TotalWeightkg' => $weight, // $weight, //  '34.000', // "34.000",
                        // 'Postage' => '2.45', // "2.45", // optional - Postage Amount // Porto
                        'InternationalDocumentFormat' => [
                            'Type' => 'PDF',
                            'Size' => $international,
                        ],
                    ],
                ],
            ],
        ];

        $service = $packageData['service'];
        if (!empty($service) && is_array($service)) {
            $services = [];
            foreach ($service as $serviceID) {
//            Parameter Services - Array of ShipmentService:
//              Parameters: optional - so far not in use, for future use.
//              ServiceID: required - Service ID, e.g. NN for COD, WERT for insurance, SA for Saturday Delivery, etc.
//              Value: optional - Currency and Value specification, e.g. for Insurance or COD. Exception for DHL-BulkyGoods: Amount determines kind of bulkgoods: 0=Lang, 1=L, 2=XL, 3=XXL, default=XXL
                $services[] = ['ServiceID' => $serviceID];
            }
            $shipment['Services'] = $services;
        }

        $request = ParcelOneRequest::getInstance($this->einstellungen);
        $response = $request->registerShipments($shipment);

        if (!array_key_exists('ActionResult', $response)) {
            throw MissingResponseFieldException::fromFieldName('ActionResult');
        }
        $number = $this->extractActionResultID($response);

        if ($this->einstellungen['autotracking'] === '1') {
            $lieferscheinID = $id;
            if (array_key_exists('lieferschein', $document)) {
                $lieferscheinID = $document['lieferschein'];
            }
            $versandID = $doctyp === 'versand' ? $id : 0;
            $this->SetTracking($number, $versandID, $lieferscheinID);
            unset($versandID, $lieferscheinID);
        }

        if (!$packageData['drucken'] && !$packageData['tracking_again']) {
            return [];
        }

        /*
         * Let's print the two documents.
         */
        if (array_key_exists('LabelsAvailable', $response) && $response['LabelsAvailable'] === 1) {
            $label = 'Label_' . $number . '.pdf';
            $content = $response['PackageResults']['ShipmentPackageResult']['Label'];
            $content = base64_decode($content, true);
            $this->printFile($label, $content, $versandId);
            unset($label, $content);
        }

        if (array_key_exists('InternationalDocumentsNeeded', $response) &&
            array_key_exists('InternationalDocumentsResults', $response) &&
            array_key_exists('InternationalDocumentsAvailable', $response) &&
            is_array($response['InternationalDocumentsResults']) &&
            !empty($response['InternationalDocumentsResults']) &&
            $response['InternationalDocumentsNeeded'] === 1 &&
            $response['InternationalDocumentsAvailable'] === 1 && !empty($response['InternationalDocumentsResults'])) {

            $internationalFile = $international . '_' . $number . '.pdf';
            $content = $response['InternationalDocumentsResults']['ShipmentDocumentsResult']['Document'];

            $content = base64_decode($content, true);
            $this->printFile($internationalFile, $content, $versandId);
        }

        return [];
    }

    /**
     * Get the custom documents format.
     *
     * @param string $country like 'DE'
     *
     * @return string of '', 'CN22' or 'CN23';
     */
    protected function getInternationalDocumentType($country)
    {
        $senderCountry = $this->app->erp->Firmendaten('land');
        if ($country === $senderCountry) {
            // no custom documents required
            return '';
        }
        if ($this->app->erp->IstEU($country)) {
            // no custom documents required
            return '';
        }

        $international = $this->einstellungen['international'];
        if (in_array($international, ['CN22', 'CN23'], true)) {
            return $international;
        }

        return 'CN23';
    }

    /**
     * Load contents & quantity for all items of one delivery.
     *
     * @param int|string $id delivery note id
     * @param string $table
     *
     * @return array
     */
    private function loadDeliveryPositions($id, $table)
    {
        if (is_string($id) && is_numeric($id)) {
            $id = (int)$id;
        }
        if (!is_int($id)) {
            $type = gettype($id);
            throw new ArgumentTypeException('Expected order id as int, got ' . $type);
        }
//        $sql = '
//SELECT
//    lp.menge,
//    lp.bezeichnung,
//    if(lp.zolleinzelwert >0 , lp.zolleinzelwert , ( ap.preis - (ap.preis / 100 * ap.rabatt ) ) ) as preis,
//    lp.zolltarifnummer,
//    if(lp.zollwaehrung != \'\' , lp.zollwaehrung, ap.waehrung) as waehrung,
//    lp.artikel,
//    lp.zolltarifnummer
//FROM
//    lieferschein_position lp
//LEFT JOIN
//    auftrag_position ap
//ON
//    ap.id = lp.auftrag_position_id
//    LEFT JOIN
//        artikel a
//    ON
//        a.id = lp.artikel
//    WHERE
//        lp.lieferschein=\'%s\'
//    AND
//        ap.explodiert != 1
//    AND
//        a.lagerartikel = 1';
        switch ($table) {
            case 'versand':
                $sql = '
SELECT
    lp.bezeichnung as Contents,
    lp.menge as Quantity,
    lp.zollgesamtgewicht as NetWeight,
    lp.zollgesamtwert as ItemValue,
    lp.herkunftsland as Origin,
    lp.zolltarifnummer as TariffNumber,
    lp.zollwaehrung as Currency
FROM
    lieferschein_position as lp,
    versand as v
WHERE
    lp.lieferschein = v.lieferschein
AND
	v.id =\'%s\'';
                break;
            case 'lieferschein':
                $sql = '
SELECT
    bezeichnung as Contents,
    menge as Quantity,
    zollgesamtgewicht as NetWeight,
    zollgesamtwert as ItemValue,
    herkunftsland as Origin,
    zolltarifnummer as TariffNumber,
    zollwaehrung as Currency
FROM
    lieferschein_position
WHERE
    lieferschein =\'%s\'';
                break;
            case 'retoure':
                $sql = '
SELECT
    bezeichnung as Contents,
    menge as Quantity,
    herkunftsland as Origin,
    zolltarifnummer as TariffNumber
FROM
    retoure_position
WHERE
    retoure =\'%s\'';
// zolleinzelgewicht as NetWeight,
// zolleinzelwert as ItemValue,
// zollwaehrung as Currency
                break;
            case 'auftrag':
                $sql = '
SELECT
    bezeichnung as Contents,
    menge as Quantity,
    zollgesamtgewicht as NetWeight,
    zollgesamtwert as ItemValue,
    herkunftsland as Origin,
    zolltarifnummer as TariffNumber,
    zollwaehrung as Currency
FROM
    auftrag_position
WHERE
    auftrag =\'%s\'';
                break;
            default:
                throw new InvalidArgumentException(sprintf('Unknown table \'%s\'.', $table));
        }

        $sql = sprintf($sql, $id);

        $positions = $this->app->DB->Query($sql);
        $error = $this->app->DB->error();

        if ($error) {
            $error = htmlspecialchars($error);
            $msg = sprintf('SQL query error: \'%s\', query was \'%s\'', $error, $sql);
            throw new RuntimeException($msg);
        }

        $positions = $positions->fetch_all(MYSQLI_ASSOC);
        $error = $this->app->DB->error();
        if ($error) {
            $error = htmlspecialchars($error);
            $msg = sprintf('SQL query error: \'%s\', query was \'%s\'', $error, $sql);
            throw new RuntimeException($msg);
        }

        if (!is_array($positions)) {
            $type = gettype($positions);
            throw new ArgumentTypeException('Expected positions as array, got ' . $type);
        }

        return (array)$positions;
    }

    /**
     * Return a tracking number / package id
     *
     * @param $response
     *
     * @throws MissingResponseFieldException
     *
     * @return string|int
     */
    private function extractActionResultID($response)
    {
        $number = null;
        if (!is_array($response)) {
            $type = gettype($response);
            throw new ArgumentTypeException('Expected response as array, got ' . $type);
        }
        if (!array_key_exists('ActionResult', $response)) {
            throw new MissingResponseFieldException('Field \'ActionResult\' in response is missing');
        }

        $actionResult = $response['ActionResult'];
        if (array_key_exists('TrackingID', $actionResult)) {
            return $actionResult['TrackingID'];
        }
        if (array_key_exists('ShipmentID', $actionResult)) {
            return $actionResult['ShipmentID'];
        }
        if (array_key_exists('ShipmentRef', $actionResult)) {
            return $actionResult['ShipmentRef'];
        }

        throw MissingResponseFieldException::fromFieldName('TrackingID/ShipmentID/ShipmentRef is missing.');
    }

    /**
     * Source: ups.php
     *
     * @param int $lieferscheinID
     * @return string
     */
    public function getProjectShortName($lieferscheinID)
    {
        $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$lieferscheinID' LIMIT 1");
        return $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$projekt' LIMIT 1");
    }

    /**
     * Source: ups.php
     *
     * @param int $lieferscheinID
     * @return int
     */
    public function getAuftragNummer($lieferscheinID)
    {
        $auftragid = $this->app->DB->Select("SELECT auftragid FROM lieferschein WHERE id='$lieferscheinID' LIMIT 1");
        if ($auftragid > 0 ) {
            return $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$auftragid' LIMIT 1");
        }

        return '';
    }
}
