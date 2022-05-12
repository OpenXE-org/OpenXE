<?php
/**
 * Swiss Payment Slip
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @copyright 2012-2016 Some nice Swiss guys
 * @author Marc Würth ravage@bluewin.ch
 * @author Manuel Reinhard <manu@sprain.ch>
 * @author Peter Siska <pesche@gridonic.ch>
 * @link https://github.com/ravage84/SwissPaymentSlip/
 */

namespace SwissPaymentSlip\SwissPaymentSlip;

/**
 * Orange Swiss Payment Slip
 *
 * Describes how an orange Swiss payment slip looks like and
 * how the various data fields of an orange payment slip are
 * placed or displayed respectively.
 *
 * The data of the fields is organized by its sister class OrangePaymentSlipData.
 *
 * @link https://www.postfinance.ch/content/dam/pf/de/doc/consult/templ/example/44218_templ_de_fr_it.pdf ESR layouting
 * template.
 * @link https://www.postfinance.ch/binp/postfinance/public/dam.7nf77hU5mnlo7r15PkmudMtCOvznYc0RFUecjFJued4.spool/content/dam/pf/de/doc/consult/templ/example/44205_templ_de_fr_it.pdf Payment slip ESR bank in CHF
 * @uses OrangePaymentSlipData To store the slip data.
 */
class OrangePaymentSlip extends PaymentSlip
{
    /**
     * The orange payment slip value object, which contains the payment slip data
     *
     * @var OrangePaymentSlipData
     */
    protected $paymentSlipData = null;

    /**
     * The height of the slip
     *
     * @var int|float
     */
    protected $slipHeight = 106; // default height of an orange slip

    /**
     * The width of the slip
     *
     * @var int|float
     */
    protected $slipWidth = 210; // default width of an orange slip

    /**
     * Determines whether the reference number should be displayed
     *
     * @var bool
     */
    protected $displayReferenceNr = true;

    /**
     * Determines whether the code line at the bottom should be displayed
     *
     * @var bool
     */
    protected $displayCodeLine = true;

    /**
     * Whether to return the reference number formatted in blocks of five (for better readability)
     *
     * @var bool
     */
    protected $referenceNrFormatted = true;

    /**
     * Whether to fill up the reference number with leading zeros,
     * only applies to the case where no banking customer ID is used.
     *
     * @var bool
     */
    protected $referenceNrFillZeros = true;

    /**
     * Attributes of the left reference number element
     *
     * @var array
     */
    protected $referenceNumberLeftAttr = [];

    /**
     * Attributes of the right reference number element
     *
     * @var array
     */
    protected $referenceNumberRightAttr = [];

    /**
     * Attributes of the code line element
     *
     * @var array
     */
    protected $codeLineAttr = [];

    /**
     * Create a new orange payment slip
     *
     * @param OrangePaymentSlipData $paymentSlipData The orange payment slip data.
     * @param float|null $slipPosX The optional X position of the slip.
     * @param float|null $slipPosY The optional Y position of the slip.
     */
    public function __construct(OrangePaymentSlipData $paymentSlipData, $slipPosX = null, $slipPosY = null)
    {
        parent::__construct($paymentSlipData, $slipPosX, $slipPosY);
    }

    /**
     * Sets the default attributes of the elements for an orange slip
     *
     * @return $this The current instance for a fluent interface.
     */
    protected function setDefaults()
    {
        parent::setDefaults();

        $this->setReferenceNumberLeftAttr(3, 60, 50, 4, null, null, 8);
        $this->setReferenceNumberRightAttr(125, 33.5, 80, 4);
        $this->setCodeLineAttr(64, 85, 140, 4, null, 'OCRB10');

        $this->setSlipBackground(__DIR__.'/Resources/img/ezs_orange.gif');

        return $this;
    }

    /**
     * Get the slip data object of the slip
     *
     * @return OrangePaymentSlipData The data object of the slip.
     */
    public function getPaymentSlipData()
    {
        return parent::getPaymentSlipData();
    }

    /**
     * Set the left reference number attributes
     *
     * @param float|null $posX The X position.
     * @param float|null $posY The Y Position.
     * @param float|null $width The width.
     * @param float|null $height The height.
     * @param string|null $background The background.
     * @param string|null $fontFamily The font family.
     * @param float|null $fontSize The font size.
     * @param string|null $fontColor The font color.
     * @param float|null $lineHeight The line height.
     * @param string|null $textAlign The text alignment.
     * @return $this The current instance for a fluent interface.
     */
    public function setReferenceNumberLeftAttr(
        $posX = null,
        $posY = null,
        $width = null,
        $height = null,
        $background = null,
        $fontFamily = null,
        $fontSize = null,
        $fontColor = null,
        $lineHeight = null,
        $textAlign = null
    ) {
        $this->setAttributes(
            $this->referenceNumberLeftAttr,
            $posX,
            $posY,
            $width,
            $height,
            $background,
            $fontFamily,
            $fontSize,
            $fontColor,
            $lineHeight,
            $textAlign
        );

        return $this;
    }

    /**
     * Set the right reference number attributes
     *
     * @param float|null $posX The X position.
     * @param float|null $posY The Y Position.
     * @param float|null $width The width.
     * @param float|null $height The height.
     * @param string|null $background The background.
     * @param string|null $fontFamily The font family.
     * @param float|null $fontSize The font size.
     * @param string|null $fontColor The font color.
     * @param float|null $lineHeight The line height.
     * @param string|null $textAlign The text alignment.
     * @return $this The current instance for a fluent interface.
     */
    public function setReferenceNumberRightAttr(
        $posX = null,
        $posY = null,
        $width = null,
        $height = null,
        $background = null,
        $fontFamily = null,
        $fontSize = null,
        $fontColor = null,
        $lineHeight = null,
        $textAlign = null
    ) {
        if ($textAlign === null) {
            $textAlign = 'R';
        }

        $this->setAttributes(
            $this->referenceNumberRightAttr,
            $posX,
            $posY,
            $width,
            $height,
            $background,
            $fontFamily,
            $fontSize,
            $fontColor,
            $lineHeight,
            $textAlign
        );

        return $this;
    }

    /**
     * Set the code line attributes
     *
     * @param float|null $posX The X position.
     * @param float|null $posY The Y Position.
     * @param float|null $width The width.
     * @param float|null $height The height.
     * @param string|null $background The background.
     * @param string|null $fontFamily The font family.
     * @param float|null $fontSize The font size.
     * @param string|null $fontColor The font color.
     * @param float|null $lineHeight The line height.
     * @param string|null $textAlign The text alignment.
     * @return $this The current instance for a fluent interface.
     */
    public function setCodeLineAttr(
        $posX = null,
        $posY = null,
        $width = null,
        $height = null,
        $background = null,
        $fontFamily = null,
        $fontSize = null,
        $fontColor = null,
        $lineHeight = null,
        $textAlign = null
    ) {
        if ($textAlign === null) {
            $textAlign = 'R';
        }

        $this->setAttributes(
            $this->codeLineAttr,
            $posX,
            $posY,
            $width,
            $height,
            $background,
            $fontFamily,
            $fontSize,
            $fontColor,
            $lineHeight,
            $textAlign
        );

        return $this;
    }

    /**
     * Get the attributes of the left reference number element
     *
     * @return array The attributes of the left reference number element.
     */
    public function getReferenceNumberLeftAttr()
    {
        return $this->referenceNumberLeftAttr;
    }

    /**
     * Get the attributes of the right reference umber element
     *
     * @return array The attributes of the right reference umber element.
     */
    public function getReferenceNumberRightAttr()
    {
        return $this->referenceNumberRightAttr;
    }

    /**
     * Get the attributes of the code line element
     *
     * @return array The attributes of the code line element.
     */
    public function getCodeLineAttr()
    {
        return $this->codeLineAttr;
    }

    /**
     * Set whether or not to display the reference number
     *
     * @param bool $displayReferenceNr True if yes, false if no
     * @return $this The current instance for a fluent interface.
     */
    public function setDisplayReferenceNr($displayReferenceNr = true)
    {
        $this->isBool($displayReferenceNr, 'displayReferenceNr');
        $this->displayReferenceNr = $displayReferenceNr;

        return $this;
    }

    /**
     * Get whether or not to display the reference number
     *
     * @return bool True if yes, false if no.
     */
    public function getDisplayReferenceNr()
    {
        if ($this->getPaymentSlipData()->getWithReferenceNumber() !== true) {
            return false;
        }
        return $this->displayReferenceNr;
    }

    /**
     * Set whether or not to display the code line at the bottom
     *
     * @param bool $displayCodeLine True if yes, false if no
     * @return $this The current instance for a fluent interface.
     */
    public function setDisplayCodeLine($displayCodeLine = true)
    {
        $this->isBool($displayCodeLine, 'displayCodeLine');
        $this->displayCodeLine = $displayCodeLine;

        return $this;
    }

    /**
     * Set whether to format the reference number for better readability
     *
     * @param bool $referenceNrFormatted True if yes, else false.
     * @return $this The current instance for a fluent interface.
     */
    public function setReferenceNrFormatted($referenceNrFormatted)
    {
        $this->isBool($referenceNrFormatted, 'referenceNrFormatted');
        $this->referenceNrFormatted = $referenceNrFormatted;
        return $this;
    }

    /**
     * Get whether to format the reference number for better readability
     *
     * @return bool True if yes, else false.
     */
    public function getReferenceNrFormatted()
    {
        return $this->referenceNrFormatted;
    }

    /**
     * Set whether to fill the reference number with zeros
     *
     * @param bool $referenceNrFillZeros True if yes, else false.
     * @return $this The current instance for a fluent interface.
     */
    public function setReferenceNrFillZeros($referenceNrFillZeros)
    {
        $this->isBool($referenceNrFillZeros, 'referenceNrFillZeros');
        $this->referenceNrFillZeros = $referenceNrFillZeros;
        return $this;
    }

    /**
     * Get whether to fill the reference number with zeros
     *
     * @return bool True if yes, else false.
     */
    public function getReferenceNrFillZeros()
    {
        return $this->referenceNrFillZeros;
    }

    /**
     * Get whether or not to display the code line at the bottom
     *
     * Overwrites the parent method as it checks additional settings.
     *
     * @return bool True if yes, false if no.
     */
    public function getDisplayCodeLine()
    {
        if ($this->getPaymentSlipData()->getWithAccountNumber() !== true ||
            $this->getPaymentSlipData()->getWithReferenceNumber() !== true
        ) {
            return false;
        }
        return $this->displayCodeLine;
    }

    /**
     * Get all elements of the slip
     *
     * @return array All elements with their lines and attributes.
     */
    public function getAllElements()
    {
        $paymentSlipData = $this->paymentSlipData;
        $formatted = $this->getReferenceNrFormatted();
        $fillZeros = $this->getReferenceNrFillZeros();

        $elements = parent::getAllElements();

        // Place left reference number
        if ($this->getDisplayReferenceNr()) {
            $lines = [
                $paymentSlipData->getCompleteReferenceNumber(
                    $formatted,
                    $fillZeros
                )
            ];
            $elements['referenceNumberLeft'] = [
                'lines' => $lines,
            'attributes' => $this->getReferenceNumberLeftAttr()
            ];

            // Place right reference number
            // Reuse lines from above
            $elements['referenceNumberRight'] = [
                'lines' => $lines,
                'attributes' => $this->getReferenceNumberRightAttr()
            ];
        }

        // Place code line
        if ($this->getDisplayCodeLine()) {
            $lines = [
                $paymentSlipData->getCodeLine($fillZeros)];
            $elements['codeLine'] = [
                'lines' => $lines,
                'attributes' => $this->getCodeLineAttr()
            ];
        }

        return $elements;
    }
}
