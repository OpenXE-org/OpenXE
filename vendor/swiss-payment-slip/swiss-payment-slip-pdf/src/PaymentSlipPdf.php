<?php
/**
 * Swiss Payment Slip PDF
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @copyright 2012-2015 Some nice Swiss guys
 * @author Marc Würth ravage@bluewin.ch
 * @author Manuel Reinhard <manu@sprain.ch>
 * @author Peter Siska <pesche@gridonic.ch>
 * @link https://github.com/ravage84/SwissPaymentSlipPdf/
 */

namespace SwissPaymentSlip\SwissPaymentSlipPdf;

use SwissPaymentSlip\SwissPaymentSlip\PaymentSlip;

/**
 * An abstract base class for concrete implementations for creating Swiss payment slips as PDFs
 *
 * Responsible for generating standard Swiss payment Slips
 * using a PDF engine, e.g. FPDF or TCPDF.
 * Layout done by utilizing PaymentSlip and
 * data organisation through PaymentSlipData.
 *
 * @link https://github.com/ravage84/SwissPaymentSlip/ SwissPaymentSlip
 * @link https://github.com/ravage84/SwissPaymentSlipTcpdf/ SwissPaymentSlipTcpdf
 * @link https://github.com/ravage84/SwissPaymentSlipFpdf/ SwissPaymentSlipFpdf
 */
abstract class PaymentSlipPdf
{
    /**
     * The PDF engine object to generate the PDF output
     *
     * @var null|object
     */
    protected $pdfEngine = null;

    /**
     * The payment slip object, which contains the payment slip data and layout information
     *
     * @var null|PaymentSlip
     */
    protected $paymentSlip = null;

    /**
     * Create a new object to create Swiss payment slips as PDFs
     *
     * @param object $pdfEngine The PDF engine object to generate the PDF output.
     * which contains the payment slip data and layout information.
     */
    public function __construct($pdfEngine)
    {
        if (!is_object($pdfEngine)) {
            throw new \InvalidArgumentException('$pdfEngine is not an object!');
        }
        $this->pdfEngine = $pdfEngine;
    }

    /**
     * Display a background image
     *
     * Implement this method by using the parameter(s) with he appropriate method of the PDF engine.
     *
     * @param string $background The background image path.
     * @return $this The current instance for a fluent interface.
     */
    abstract protected function displayImage($background);

    /**
     * Set the font of an element
     *
     * Implement this method by using the parameter(s) with he appropriate method of the PDF engine.
     *
     * @param string $fontFamily The font family
     * @param int|float $fontSize The font size.
     * @param string $fontColor The font color. Either the name of the color or its RGB hex code.
     * @return $this The current instance for a fluent interface.
     */
    abstract protected function setFont($fontFamily, $fontSize, $fontColor);

    /**
     * Set the background of an element
     *
     * Implement this method by using the parameter(s) with he appropriate method of the PDF engine.
     *
     * @param $background
     * @return $this The current instance for a fluent interface.
     */
    abstract protected function setBackground($background);

    /**
     * Set the position of an element
     *
     * Implement this method by using the parameter(s) with he appropriate method of the PDF engine.
     *
     * @param int|float $posX The X position.
     * @param int|float $posY The Y position.
     * @return $this The current instance for a fluent interface.
     */
    abstract protected function setPosition($posX, $posY);

    /**
     * Create the element cell using the PDF engine
     *
     * Implement this method by using the parameter(s) with he appropriate method of the PDF engine.
     *
     * @param int|float $width The width.
     * @param int|float $height The height.
     * @param string $line The text/content of the line.
     * @param string $textAlign The text alignment.
     * @param bool $fill Whether to fill the background of the cell.
     * @return $this The current instance for a fluent interface.
     */
    abstract protected function createCell($width, $height, $line, $textAlign, $fill);

    /**
     * Write the lines of an element to the PDf
     *
     * @param string $elementName The name of the element.
     * @param array $element The element.
     * @return $this The current instance for a fluent interface.
     */
    protected function writePaymentSlipLines($elementName, array $element)
    {
        if (!is_string($elementName)) {
            throw new \InvalidArgumentException('$elementName is not a string!');
        }
        if (!isset($element['lines'])) {
            throw new \InvalidArgumentException('$element contains not "lines" key!');
        }
        if (!isset($element['attributes'])) {
            throw new \InvalidArgumentException('$element contains not "attributes" key!');
        }
        $lines = $element['lines'];
        $attributes = $element['attributes'];

        if (!is_array($lines)) {
            throw new \InvalidArgumentException('$lines is not an array!');
        }
        if (!is_array($attributes)) {
            throw new \InvalidArgumentException('$attributes is not an array!');
        }

        $posX = $attributes['PosX'];
        $posY = $attributes['PosY'];
        $height = $attributes['Height'];
        $width = $attributes['Width'];
        $fontFamily = $attributes['FontFamily'];
        $background = $attributes['Background'];
        $fontSize = $attributes['FontSize'];
        $fontColor = $attributes['FontColor'];
        $lineHeight = $attributes['LineHeight'];
        $textAlign = $attributes['TextAlign'];

        $this->setFont($fontFamily, $fontSize, $fontColor);
        if ($background != 'transparent') {
            $this->setBackground($background);
            $fill = true;
        } else {
            $fill = false;
        }

        foreach ($lines as $lineNr => $line) {
            $this->setPosition(
                $this->paymentSlip->getSlipPosX() + $posX,
                $this->paymentSlip->getSlipPosY() + $posY + ($lineNr * $lineHeight)
            );
            $this->createCell($width, $height, $line, $textAlign, $fill);
        }

        return $this;
    }

    /**
     * Create a payment slip as a PDF using the PDF engine
     *
     * @param PaymentSlip $paymentSlip The payment slip to create as PDF.
     * @return $this The current instance for a fluent interface.
     */
    public function createPaymentSlip(PaymentSlip $paymentSlip)
    {
        $this->paymentSlip = $paymentSlip;

        // Place background image
        if ($paymentSlip->getDisplayBackground()) {
            $this->displayImage($paymentSlip->getSlipBackground());
        }

        $elements = $paymentSlip->getAllElements();

        // Go through all elements, write each line
        foreach ($elements as $elementName => $element) {
            $this->writePaymentSlipLines($elementName, $element);
        }

        $this->paymentSlip = null;

        return $this;
    }
}
