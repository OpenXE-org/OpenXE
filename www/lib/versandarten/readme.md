
# lib Versandarten

Jede Versandart besteht aus einer .php-Datei. Zusätslich können unter `content` weitere *.tpl Dateien abgelegt werden.

Der Klassenname besteht aus `Versandart_{dateiname}`.

Die Klasse muss von der Klasse `Versanddienstleister` erben, welche wenn nötig noch importiert werden muss:

```php
<?php
if (!class_exists('Versanddienstleister')) {
    require_once dirname(__DIR__) . '/class.versanddienstleister.php';
}

class Versandart_example extends Versanddienstleister
{
    // ...
}
```
## ZukunftsWünsche:
Wenn die Versandarten überarbeitet werden oder in den neuen Codebereich übernommen werden, wäre 
eine **mehrstufige GUI** zum Einrichten der Versandarten wünschenswert. Dies wird mindestens in den Versandarten
``Parcelone`` und ``Sendcloud`` verwendet. 
- In Stufe #1 werden die API-Zugangsdaten abgefragt.
- In Stufe #2 werden zusätzliche Daten von der API abgeholt (und zur Auswahl gestellt).

## Methoden

Folgende Methoden müssen implementiert werden:
- `public function __construct($app, $id)`
- `public function GetBezeichnung()`
- `public function EinstellungenStruktur()`
- `public function Paketmarke($doctyp, $id, $target = '', $error = false, &$adressdaten = null)`
- `public function PaketmarkeDrucken($id, $sid)`


### __construct

`public function __construct($app, $id)`

Im Konstruktor werden die Einstellungen der Versandart geladen. Ebenso werden der Paketmarken- & Exportdrucker geladen.

### GetBezeichnung

```php
/**
 * @return string
 */
public function GetBezeichnung()
```

Ich glaube, diese Methode wird gar nicht verwendet.
Der Name der Versandarten wird in der Methode Appstore::getAppList() in einem Array hinterlegt.
Zu finden ist diese Methode in ``www/pages/appstore.php``.
Der Aufbau sieht wie folgt aus:

```php
    $apps = array(
        // ...
        'versandarten_dpdesolutions' [
            'Bezeichnung' => 'DPD eSolutions',
            'Link' => 'index.php?module=versandarten&action=list',
            'Icon' =>' Icons_dunkel_9.gif',
            'Versionen' => 'OSS,PRE,ENT',
            'kategorie' => '{|Versandarten|}')
        ],
    );
```

### EinstellungenStruktur

```php
/**
 * @return array
 */
public function EinstellungenStruktur()
```

Erstellt die Eingabenmaske unter `/index.php?module=versandarten&action=create`

Unterstützt werden derzeit folgende Felder:
- textarea
- checkbox
- select
- submit
- custom (Key 'function' als callback)

### Paketmarke

```php
/**
 * @param string $doctyp 'lieferschein' / 'versand' / 'retoure'
 * @param string|int $id '1'
 * @param string $target '#TAB1'
 * @param bool $error
 * @param null|array $adressdaten
 *
 * @return array List of error messages
 */
public function Paketmarke($doctyp, $id, $target = '', $error = false, &$adressdaten = null)
```

Diese Methode wird von folgenden Endpunkten aus aufgerufen:
- [Lager -> Lieferschein](index.php?module=lieferschein&action=paketmarke&id={id})
- [Lager -> Versandzentrum](index.php?module=versanderzeugen&action=frankieren&id={id})
- [Lager -> Retoure](index.php?module=retoure&action=paketmarke&id={id})

Die Variable `$adressdaten` ist in diesem Fall null.

Zudem wird diese Methode von `PaketmarkeDrucken()` aufgerufen. In diesem Fall enthält das Array `$adressdaten` die Adressdaten. Sie müseen in diesem Fall *nicht* via `$this->app->Secure->GetPOST()` abgefragt werden.

Der Parameter `$doctyp` enthält, abhängig von dem verwendeten Endpunkt eine der folgenden Strings:
- lieferschein
- versand
- retoure

`$id` enthält die ID (als String oder Integer) der auszuwertenden Resource.

`$target` enthält einen String wie '#TAB1' oder ist leer. Ist dieser Wert gesetzt, muss das Template geparsed werden. Ein leerer String deutet vermutlich auf einen *internen* Aufruf ohne GUI hin.

Genutzt wird diese Methode in `erpAPI::Paketmarke()`

@file class.erpapi.php:21640 (Stand 06.2019)
```php
<?php
//WAWICORE
class erpAPI
{
    // ...
    //@refactor versanddiestleister Modul
    function Paketmarke($parsetarget,$sid="",$zusatz="",$typ="DHL")
    {
      // ...
      // 22042
      $error = $obj->Paketmarke($sid!=''?$sid:'lieferschein',$id);
      // ...
      // 22568
      $error = $obj->Paketmarke($sid!=''?$sid:'lieferschein',($sid=='versand'?$id:$tid), $parsetarget, $error);
      // ...
    }
    // ...
}
```

### PaketmarkeDrucken

```php
/*
 * @param int|string $id
 * @param $sid
 *
 * @return array List of error messages
 */
public function PaketmarkeDrucken($id, $sid)
```

`$sid` enthält einen der Strings welche `Paketmarke` als `$doctyp` übergeben werden.

`$id` entspricht der ID des jeweiligen Datensatzes.

Genutzt wird diese Methode in `erpAPI::PaketmarkeDrucken()`

@file class.erpapi.php:22846 (Stand 06.2019)
```php
<?php
//WAWICORE
class erpAPI
{
    // ...
    //@refactor versanddiestleister Modul
    function PaketmarkeDrucken($id, $sid = 'lieferschein')
    {
      // ...
      if($ret = $obj->PaketmarkeDrucken($lieferschein, 'lieferschein'))
      // ...
    }
    // ...
}
```

### TrackingReplace

```php
  /**
   * @param string $tracking
   *
   * @return string
   */
  public function TrackingReplace($tracking)
```

Genutzt wird diese Methode in `erpAPI::Paketmarke()`

@file class.erpapi.php:21703 (Stand 06.2019)
```php
<?php
//WAWICORE
class erpAPI
{
    // ...
    //@refactor versanddiestleister Modul
    function Paketmarke($parsetarget,$sid="",$zusatz="",$typ="DHL")
    {
      // ...
      $tracking = $obj->TrackingReplace($tracking);
      // ...
    }
    // ...
}
```


## Abstrakte Klasse Versandart

Um neue Versandarten schneller und einfacher anzulegen, existiert in der Versandart *ParcelOne* eine abstrakte Klasse, welche die geforderten Methoden mit einer default Implementierung beinhaltet oder diese explizit als abstrakte Methode definiert. Siehe `AbstractVersandartParcelone`. Diese Klasse *muss* nicht verwendet werden, deckt aber bereits einige der Anforderungen ab.

Die abstrakte Klasse erbt wie gefordert von der Klasse ``Versanddienstleister``.

Um diese Klasse zu verwenden müssen folgende Methoden implementiert werden:

- EinstellungenStruktur
- parseTemplate
- createPaketmarke

Alle anderen gefoderten Methoden stehen mit einer default Implementierung zur Verfügung. Diese können zum Teil überschrieben werden.

Weiterhin werden einige nützliche Hilfsmethoden bereitgestellt.

### Variablen:

Folgende Variablen werden von allen Versandarten benötigt und sind deshalb hier definiert:

```php
    public $einstellungen = [];
    public $export_drucker;
    public $paketmarke_drucker;    
```

### Konstruktor:

Der Konstruktor ist bereits implementiert und lädt die nötigen Einstellungen aus der Datenbank. Er ist als 'final' deklariert, kann folglich nicht überschrieben werden.

```php
    /**
     * @param app_t $app
     * @param int $id
     */
    final public function __construct($app, $id){}
```

### Abstrakte Methoden:

Nachfolgende Methoden wurden als abstract deklariert und müssen zwingend implementiert werden:

#### EinstellungenStruktur:

Erstellt die Eingabenmaske unter `/index.php?module=versandarten&action=create`

```php    
    /**
     * @return array
     */
    abstract public function EinstellungenStruktur();
```

#### parseTemplate:

Das Template-file ist nicht (zwingend) bekannt. Verschiedene Platzhalter sind abhängig von der verwendeten Versandart. Deshalb ist die Methode ``parseTemplate`` als abstract vorgesehen.

```php
    /**
     * @param string $target
     *
     * @return void
     */
    abstract protected function parseTemplate($target);
```

#### createPaketmarke:

Das 'Herzstück' der Paketmarke ist die Methode ``createPaketmarke()`` welche die Methoden ``Paketmarke()`` und ``PaketmarkeDrucken()`` eint.

Diese Methode muss sich nicht mehr darum kümmern, von welchem Endpunkt sie aufgerufen wurde und ob die Variable ``$adressdaten`` null ist oder tatsächlich Daten enthält.

Stattdessen wird das Array ``$adressdaten`` von den aufrufenden Methoden gefüllt. Zudem wird das Array ``$packageData`` übergeben, welches Daten zum versendeten Paket enthält.

Somit muss nur noch diese Methode implementiert werden. ``Paketmarke()`` und ``PaketmarkeDrucken()`` können in der default Implementierung genutzt werden.

```php
    /**
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
```

### Default Implementierungen:

Folgende Methoden existieren mit einer default implementierung und können bei bedarf überschrieben werden:

#### TrackingReplace:

``TrackingReplace()`` ändert momentan die Trackingnummer nicht.

```php
    /**
     * @param string $tracking
     * 
     * @return string
     */
    public function TrackingReplace($tracking)
    {
      return $tracking;
    }
```

#### GetBezeichnung:

Erstellt die Bezeichnung anhand des Klassennamens der aktuellen Versandart. Z.B. wird '*Versandart_parcelone*' zu '*Parcelone*' konvertiert.

```php 
    /**
     * @return string
     */
    public function GetBezeichnung()
```

#### validateSettings:

Die Methode ``validateSettings()`` prüft die getätigten Einstellungen anhand von ``EinstellungenStruktur()`` auf Plausibilität. 

Es wird geprüft, ob alle nötigen Felder ausgefüllt wurden. Bei Drop-Down Auswahlfeldern wird zudem geprüft, ob die Auswahl tatsächlich erlaubt ist.

```php
    /**
     * @throws RuntimeException im Fehlerfall
     * @return void
     */
    protected function validateSettings()
```

#### getUserName:

Enthält eine Hilfsmethode um den Nutzernamen abzufragen. Der String wird mittels ``real_escape_string()`` behandelt.

```php
    /**
     * @return string
     */
    protected function getUserName()
```

#### Paketmarke:

Hier ist eine als `final` deklarierte Implementation der Methode `Paketmarke()`. Sie Prüft die Variablen `$doctyp` und `$id` und lädt die entsprechenden Daten aus der DB. Über `validateSettings()` werden die Einstellungen geprüft.

Die Addressdaten und Paketdaten werden aus dem Parameter `$adressdaten` oder aus den `$_POST` Feldern ausgelesen. Die benötigten Daten stehen nun in den Variablen `$address` und `$packageData` zur Verfügung, unabhängig von ihrer Quelle.

Mit diesen Daten wird anschließend die abstrakte Methode `createPaketmarke()` aufgerufen.

```php
    /**
     * @param string $doctyp 'lieferschein' / 'versand' / 'retoure'
     * @param string|int $id '1'
     * @param string $target '#TAB1'
     * @param bool $error
     * @param null|array $adressdaten
     *
     * @return array
     */
    final public function Paketmarke($doctyp, $id, $target = '', $error = false, &$adressdaten = null)
```

#### PaketmarkeDrucken:

Über die default implementierung von `PaketmarkeDrucken()` wird ebenso die abstrakte Methode `createPaketmarke()` aufgerufen.

```php
    /**
     * @param int|string $id
     * @param $sid
     *
     * @return array
     */
    public function PaketmarkeDrucken($id, $sid)
```

#### getDocumentByID:

Über die Methode `getDocumentByID()` werden die Daten geladen, welche über einen Dokumenttyp und dessen ID identifiert sind.

Der Dokumenttyp sollte einen der folgenden Werte haben:
- 'lieferschein'
- 'versand'
- 'retoure'
- 'auftrag'

Dieser Wert wird nicht geprüft. Damit ist es möglich, auch andere Dokumente zu laden.

```php
    /**
     * @param string $documentTyp on of 'lieferschein', 'versand' or 'retoure'
     * @param int $id
     *
     * @throws RuntimeException
     *
     * @return array
     */
    protected function getDocumentByID($documentTyp, $id)
```

#### getPrinter

Durch die Methode `getPrinter()` wird die ID des Standard-Paketmarkendruckers 

zurückgegeben, falls dies über die Parameter `drucken` bzw `tracking_again` gefordert ist. Ansonsten wird der Wert `0` zurückgegeben.
```php
    /**
     * @return int
     */
    protected function getPrinter()
```
