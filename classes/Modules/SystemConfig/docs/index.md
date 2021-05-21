# SystemConfig

## Neue SystemConfig-Instanz erzeugen


```php
/** @var \Xentral\Modules\SystemConfig\SystemConfigModule $config */
$systemConfig = $container->get('SystemConfigModule');
```

## Validierung von Namespace und Key

Namespace und Key dürfen nur folgende Zeichen enthalten: 
* Buchstaben im lowercase
* Zahlen
* Unterstriche

Alle anderen Zeichen sind nicht erlaubt und führen zu einer `InvalidArgumentException`.  
Zusätzlich darf die Anzahl der Zeichen von Namespace + Key nicht größer als 244 sein, 
da sonst ebenso eine `InvalidArgumentException` geworfen wird.

## Maximale Wertgröße

Die maximale Größe eines zu speichernden Wertes ist 64kB.

## Wert speichern

Für die Speicherung eines Wertes werden ein Namespace und Key benötigt.  
Der Namespace ist der Modulname, für den die Konfiguration gespeichert werden soll. 
Der Key kann frei gewählt werden.  
Wenn ein Namespace-Key Paar beim Speichern noch nicht in der Datenbank existiert, 
wird es neu erstellt.

```php
/** @var \Xentral\Modules\SystemConfig\SystemConfigModule $config */
$systemConfig = $container->get('SystemConfigModule');
$namespace = 'report';
$key = 'last_value_of_element_1';
$value = 'checked';

$systemConfig->setValue($namespace, $key, $value);
```

## Wert auslesen

Für das Auslesen eines Wertes werden wieder der Namespace und Key benötigt. 
Es stehen zwei Funktionen zur Auswahl: `getValue()` sowie `tryGetValue()`.  
Falls kein Wert zu einem gegeben Namespace-Key Paar gefunden werden konnte, wirft die getValue() 
`ConfigurationKeyNotFoundException`. Die Funktion `tryGetValue()` gibt in so einem Fall den optionalen 
default Parameter zurück.

```php
/** @var \Xentral\Modules\SystemConfig\SystemConfigModule $config */
$systemConfig = $container->get('SystemConfigModule');
$namespace = 'report';
$key = 'last_value_of_element_1';

$value = $systemConfig->tryGetValue($namespace, $key, 'fallback');

try {
    /** @var string $response */
    $value = $systemConfig->getValue($namespace, $key);
} catch (\Xentral\Modules\SystemConfig\Exception\InvalidArgumentException $exception) {
    // Für das Namespace-Key Paar existiert noch kein Eintrag in der Datbenank
}
```

## Schlüssel auf Existenz prüfen

Es ist möglich Schlüssel auf Existenz zu prüfen. 

```php
/** @var \Xentral\Modules\SystemConfig\SystemConfigModule $config */
$systemConfig = $container->get('SystemConfigModule');
$namespace = 'report';
$key = 'last_value_of_element_1';

$keyExists = $systemConfig->isKeyExisting($namespace, $key);
```

## Datenbankeintrag löschen

Bei Bedarf können Schlüssel samt Wert aus der Datenbank entfernt werden.

```php
/** @var \Xentral\Modules\SystemConfig\SystemConfigModule $config */
$systemConfig = $container->get('SystemConfigModule');
$namespace = 'report';
$key = 'last_value_of_element_1';

$systemConfig->deleteKey($namespace, $key);
```

## Serialisierung von Objekten

Es ist möglich für ein Modul je ein Konfigurationsobjekt zu speichern. Dazu muss das 
SystemConfigSerializableInterface implementiert werden.

```php
<?php
​
declare(strict_types=1);
​
use Xentral\Modules\SystemConfig\Interfaces\SystemConfigSerializableInterface;
​
final class ExampleSystemConfig implements SystemConfigSerializableInterface
{
    /** @var string|null $voucherArticle */
    private $voucherArticle = null;
​
    /** @var int $codeLength */
    private $codeLength = 8;
​
    /**
     * @return string
     */
    public static function getSystemConfigNamespace(): string
    {
        return 'example';
    }
​
    /**
     * @return string
     */
    public static function getSystemConfigKey(): string
    {
        return 'voucher_settings';
    }
​
    /**
     * @return bool
     */
    public function hasVoucherArticle(): bool
    {
        return !is_null($this->voucherArticle);
    }
​
    /**
     * @return string|null
     */
    public function getVoucherArticle(): ?string
    {
        return $this->voucherArticle;
    }
​
    /**
     * @return int
     */
    public function getCodeLength(): int
    {
        return $this->codeLength;
    }
​
    /**
     * @param string|null $voucherArticle
     *
     * @throws InvalidArgumentException
     */
    public function setVoucherArticle(?string $voucherArticle): void
    {
        if (is_string($voucherArticle) && empty(trim($voucherArticle))) {
            throw new InvalidArgumentException('Gutschein-Artikelnummer darf kein Leer-String sein.');
        }
        $this->voucherArticle = $voucherArticle;
    }
​
    /**
     * @param int $codeLength
     *
     * @throws InvalidArgumentException
     */
    public function setCodeLength(int $codeLength): void
    {
        if ($codeLength < 6) {
            throw new InvalidArgumentException('Mindestlänge für Gutschein-Codes sind sechs Zeichen.');
        }
        $this->codeLength = $codeLength;
    }
​
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'voucher_article' => $this->voucherArticle,
            'code_length'     => $this->codeLength,
        ];
    }
​
    /**
     * @param array $data
     *
     * @throws InvalidArgumentException
     *
     * @return self
     */
    public static function fromArray(array $data): SystemConfigSerializableInterface
    {
        $instance = new self();
​
        if (isset($data['voucher_article'])) {
            $instance->setVoucherArticle($data['voucher_article']);
        }
        if (isset($data['code_length'])) {
            $instance->setCodeLength($data['code_length']);
        }
​
        return $instance;
    }
}
```

### Objekte speichern

Das Objekt kann anschließend über die `setObject()` Funktion gespeichert werden.

```php
$object = new ClassThatUtilizesSystemConfig([1,'a', 'b' => 'c']);

/** @var \Xentral\Modules\SystemConfig\SystemConfigModule $config */
$systemConfig = $container->get('SystemConfigModule');

$systemConfig->setObject($object);
```

### Objekte laden

Die `getObject()` Funktion liefert ein Object der übergebenen Klasse. Sollte für die Klasse noch keine
Konfiguration gespeichert worden sein, sprich der Schlüssel noch nicht in der Datenbank existieren, wird
eine `ConfigurationKeyNotFoundException` geworfen.

```php
/** @var \Xentral\Modules\SystemConfig\SystemConfigModule $config */
$systemConfig = $container->get('SystemConfigModule');

$object = $systemConfig->getObject(ClassThatUtilizesSystemConfig::class);
```
