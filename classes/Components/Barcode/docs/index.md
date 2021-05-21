# Barcodes

## QR-Codes

### Beispiel

```php
$factory = $this->app->Container->get('BarcodeFactory');

// Qrcode-Objekt erzeugen
$ecLevel = 'M'; // M = Medium error correction
$qrcode = $factory->createQrCode($qrtext, $ecLevel);

// Varianten für die Ausgabe 
$html = $qrcode->toHtml($width, $height, $color);
$svg = $qrcode->toSvg($width, $height, $color);
$png = $qrcode->toPng($width, $height, $color);
```

##### Fehlerkorrektur-Levels

* `L` = Low / Niedrige Fehlerkorrektur (Default)
* `M` = Medium / Mittlere Fehlerkorrektur
* `Q` = Quartile / Bessere Fehlerkorrektur
* `H` = High / Höchste Fehlerkorrektur
