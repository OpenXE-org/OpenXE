<?php

namespace Xentral\Components\Barcode;

use TCPDF2DBarcode;
use Xentral\Components\Barcode\Exception\BarcodeCreationFailedException;
use Xentral\Components\Barcode\Exception\InvalidArgumentException;
use Xentral\Components\Barcode\Exception\MissingPhpExtensionException;

/**
 * Anti-Corruption-Layer for TCPDF2DBarcode class
 */
final class TcpdfBarcode2d
{
    /** @var array $validTypes */
    public static $validTypes = [
        Qrcode::TYPE_DEFAULT,
        Qrcode::TYPE_EC_LOW,
        Qrcode::TYPE_EC_MEDIUM,
        Qrcode::TYPE_EC_QUARTILE,
        Qrcode::TYPE_EC_HIGH,
    ];

    /** @var TCPDF2DBarcode $barcode */
    private $barcode;

    /** @var string $codeType */
    private $type;

    /** @var string $codeText */
    private $text;

    /**
     * @param string $type
     * @param string $text
     *
     * @throws InvalidArgumentException
     * @throws BarcodeCreationFailedException
     */
    public function __construct($type, $text)
    {
        if (empty($type)) {
            throw new InvalidArgumentException('Could not create barcode. Required parameter "type" is empty.');
        }
        if (empty($text)) {
            throw new InvalidArgumentException('Could not create barcode. Required parameter "text" is empty.');
        }
        if (!in_array($type, self::$validTypes, true)) {
            throw new InvalidArgumentException(sprintf(
                'Could not create barcode. Invalid Type: "%s". Valid types: [%s]',
                $type,
                implode('|', self::$validTypes)
            ));
        }

        $barcode = new TCPDF2DBarcode($text, $type);
        if ($barcode->getBarcodeArray() === false) {
            throw new BarcodeCreationFailedException(sprintf(
                'Could not create barcode. Type "%s" - Text "%s"', $type, $text
            ));
        }

        $this->barcode = $barcode;
        $this->text = $text;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getBarcodeArray()
    {
        return $this->barcode->getBarcodeArray();
    }

    /**
     * Returns the QR code as HTML representation
     *
     * @param int    $width  Width of a single rectangle element in pixels.
     * @param int    $height Height of a single rectangle element in pixels.
     * @param string $color  Foreground color for bar elements (background is transparent).
     *
     * @return string HTML code
     */
    public function getBarcodeHtml($width = 10, $height = 10, $color = 'black')
    {
        return $this->barcode->getBarcodeHTML($width, $height, $color);
    }

    /**
     * Returns the QR code as SVG document
     *
     * @param int    $width  Width of a single rectangle element in user units
     * @param int    $height Height of a single rectangle element in user units
     * @param string $color  Foreground color (in SVG format) for bar elements (background is transparent)
     *
     * @return string SVG document
     */
    public function getBarcodeSvg($width = 10, $height = 10, $color = 'black')
    {
        return $this->barcode->getBarcodeSVGcode($width, $height, $color);
    }

    /**
     * Returns the QR code as PNG image (requires GD or Imagick library)
     *
     * @param int   $width  Width of a single rectangle element in pixels
     * @param int   $height Height of a single rectangle element in pixels
     * @param array $color  RGB-Array (0-255) foreground color for bar elements (background is transparent)
     *
     * @throws MissingPhpExtensionException If gd and imagick extension missing
     * @throws BarcodeCreationFailedException
     *
     * @return string Image as string
     */
    public function getBarcodePng($width, $height, $color = [0, 0, 0])
    {
        $imageData = $this->barcode->getBarcodePngData($width, $height, $color);
        if (!function_exists('imagecreate') && !extension_loaded('imagick')) {
            throw new MissingPhpExtensionException(
                'Barcode image creation failed. PHP extension "gd" or "imagick" is required; both missing.'
            );
        }
        if ($imageData === false) {
            throw new BarcodeCreationFailedException(
                'Barcode image creation failed. Unknown error.'
            );
        }

        return $imageData;
    }
}
