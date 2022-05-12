<?php
/**
 * Swiss Payment Slip FPDF
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @copyright 2012-2015 Some nice Swiss guys
 * @author Marc Würth <ravage@bluewin.ch>
 * @author Manuel Reinhard <manu@sprain.ch>
 * @author Peter Siska <pesche@gridonic.ch>
 * @link https://github.com/ravage84/SwissPaymentSlipFpdf
 */

namespace SwissPaymentSlip\SwissPaymentSlipFpdf;

use SwissPaymentSlip\SwissPaymentSlipPdf\PaymentSlipPdf;
use fpdf\FPDF;

/**
 * Create Swiss payment slips (ESR/ES) as PDFs with FPDF
 *
 * Responsible for generating standard Swiss payment Slips as PDFs using FPDF as engine.
 * Layout done by utilizing PaymentSlip and
 * data organisation through PaymentSlipData.
 *
 * @link https://github.com/ravage84/SwissPaymentSlipPdf/ SwissPaymentSlipPdf
 * @link https://github.com/ravage84/SwissPaymentSlip/ SwissPaymentSlip
 */
class PaymentSlipFpdf extends PaymentSlipPdf
{
    /**
     * A caching table for hex to RGB conversions
     *
     * @var array
     */
    protected $rgbColors = array();

    /**
     * The PDF engine object to generate the PDF output with
     *
     * @var null|FPDF The PDF engine object
     */
    protected $pdfEngine = null;

    /**
     * The last set font family, prevents the PDF engine to re-set the same values over and over
     *
     * @var string;
     */
    protected $lastFontFamily = '';

    /**
     * The last set font size, prevents the PDF engine to re-set the same values over and over
     *
     * @var int|double;
     */
    protected $lastFontSize = '';

    /**
     * The last set font color, prevents the PDF engine to re-set the same values over and over
     *
     * @var string;
     */
    protected $lastFontColor = '';

    /**
     * {@inheritDoc}
     */
    protected function displayImage($background)
    {
        // TODO check if slipBackground is a color or a path to a file

        $this->pdfEngine->Image(
            $background,
            $this->paymentSlip->getSlipPosX(),
            $this->paymentSlip->getSlipPosY(),
            $this->paymentSlip->getSlipWidth(),
            $this->paymentSlip->getSlipHeight(),
            strtoupper(substr($background, -3, 3))
        );

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function setFont($fontFamily, $fontSize, $fontColor)
    {
        if ($fontColor) {
            if ($this->lastFontColor != $fontColor) {
                $this->lastFontColor = $fontColor;

                $rgbArray = $this->convertColor2Rgb($fontColor);
                $this->pdfEngine->SetTextColor($rgbArray['red'], $rgbArray['green'], $rgbArray['blue']);
            }
        }
        if ($this->lastFontFamily != $fontFamily || $this->lastFontSize != $fontSize) {
            $this->lastFontFamily = $fontFamily;
            $this->lastFontSize = $fontSize;

            $this->pdfEngine->SetFont($fontFamily, '', $fontSize);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function setBackground($background)
    {
        // TODO check if it's a path to a file
        // TODO else it should be a color
        $rgbArray = $this->convertColor2Rgb($background);
        $this->pdfEngine->SetFillColor($rgbArray['red'], $rgbArray['green'], $rgbArray['blue']);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function setPosition($posX, $posY)
    {
        $this->pdfEngine->SetXY($posX, $posY);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function createCell($width, $height, $line, $textAlign, $fill)
    {
        $this->pdfEngine->Cell($width, $height, $line, 0, 0, $textAlign, $fill);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function convertColor2Rgb($color)
    {
        if (isset($this->rgbColors[$color])) {
            return $this->rgbColors[$color];
        }
        $this->rgbColors[$color] = $this->hex2RGB($color);
        return $this->rgbColors[$color];
    }

    /**
     * Convert hexadecimal color code into an associative array or string of RGB values
     *
     * @param string $hexStr The hexadecimal color code (3 or 6 characters long)
     * @param bool $returnAsString Whether to return as a string or array.
     * @param string $separator The separator for the RGB value string, defaults to ",".
     * @return array|string|false The RGB values as an associative array or a string.
     * False if an invalid color code was given.
     *
     * @copyright 2010 hafees at msn dot com
     * @link http://www.php.net/manual/en/function.hexdec.php#99478
     * @todo Throw an exception if an invalid hex color code was given
     */
    protected function hex2RGB($hexStr, $returnAsString = false, $separator = ',')
    {
        $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
        $rgbArray = array();
        if (strlen($hexStr) == 6) {
            // If a proper hex code, convert using bitwise operation. No overhead... faster
            $colorVal = hexdec($hexStr);
            $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
            $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
            $rgbArray['blue'] = 0xFF & $colorVal;
        } elseif (strlen($hexStr) == 3) {
            // If shorthand notation, need some string manipulations
            $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
            $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
            $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
        } else {
            return false; //Invalid hex color code
        }

        // Returns the RGB values either as string or as associative array
        return $returnAsString ? implode($separator, $rgbArray) : $rgbArray;
    }
}
