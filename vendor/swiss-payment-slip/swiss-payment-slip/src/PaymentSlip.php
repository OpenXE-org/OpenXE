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

use InvalidArgumentException;

/**
 * Swiss Payment Slip
 *
 * A base class to describe the common look and field placement/display
 * of the various types of Swiss payment slips.
 *
 * The data of the fields is organized by its sister class PaymentSlipData.
 *
 * @link https://www.postfinance.ch/en/cust/download/bizdoc.html Various documents for Post business customers
 * @uses PaymentSlipData To store the slip data.
 *
 * @todo Include EUR framed slip image (701) --> back side!
 * @todo Include EUR boxed slip image (701) --> back side!
 * @todo Include CHF boxed slip image (609, ESR+)
 * @todo Implement cash on delivery (Nachnahme)
 * @todo Include cash on delivery (Nachnahme) slip image
 * @todo Create constants for the attribute keys
 * @todo Create constants for left, right and center text alignment (L, R, C)
 * @todo Create central cell placement and formatting code (lines as array, attributes)...
 * @todo Consider extracting the attributes as separate class
 */
abstract class PaymentSlip
{
    /**
     * The payment slip value object, which contains the payment slip data
     *
     * @var PaymentSlipData
     */
    protected $paymentSlipData = null;

    /**
     * Starting X position of the slip in mm
     *
     * @var int|float
     */
    protected $slipPosX = 0;

    /**
     * Starting Y position of the slip in mm
     *
     * @var int|float
     */
    protected $slipPosY = 191;

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
     * Background of the slip
     *
     * Can be either 'transparent', a color or an image
     *
     * @var null|string
     */
    protected $slipBackground = null;

    /**
     * The default font family
     *
     * @var string
     */
    protected $defaultFontFamily = 'Helvetica';

    /**
     * The default font size
     *
     * @var string
     */
    protected $defaultFontSize = '10';

    /**
     * The default font color
     *
     * @var string
     */
    protected $defaultFontColor = '#000';

    /**
     * The default line height
     *
     * @var int
     */
    protected $defaultLineHeight = 4;

    /**
     * The default text alignment
     *
     * @var string
     */
    protected $defaultTextAlign = 'L';

    /**
     * Determines whether the background should be displayed
     *
     * @var bool
     */
    protected $displayBackground = true;

    /**
     * Determines whether the bank details should be displayed
     *
     * @var bool
     */
    protected $displayBank = true;

    /**
     * Determines whether the recipient details should be displayed
     *
     * @var bool
     */
    protected $displayRecipient = true;

    /**
     * Determines whether the account should be displayed
     *
     * @var bool
     */
    protected $displayAccount = true;

    /**
     * Determines whether the amount should be displayed
     *
     * @var bool
     */
    protected $displayAmount = true;

    /**
     * Determines whether the payer details should be displayed
     *
     * @var bool
     */
    protected $displayPayer = true;

    /**
     * Attributes of the left bank element
     *
     * @var array
     */
    protected $bankLeftAttr = [];

    /**
     * Attributes of the right bank element
     *
     * @var array
     */
    protected $bankRightAttr = [];

    /**
     * Attributes of the left recipient element
     *
     * @var array
     */
    protected $recipientLeftAttr = [];

    /**
     * Attributes of the right recipient element
     *
     * @var array
     */
    protected $recipientRightAttr = [];

    /**
     * Attributes of the left account element
     *
     * @var array
     */
    protected $accountLeftAttr = [];

    /**
     * Attributes of the right account element
     *
     * @var array
     */
    protected $accountRightAttr = [];

    /**
     * Attributes of the left francs amount element
     *
     * @var array
     */
    protected $amountFrancsLeftAttr = [];

    /**
     * Attributes of the right francs amount element
     *
     * @var array
     */
    protected $amountFrancsRightAttr = [];

    /**
     * Attributes of the left cents amount element
     *
     * @var array
     */
    protected $amountCentsLeftAttr = [];

    /**
     * Attributes of the right cents amount element
     *
     * @var array
     */
    protected $amountCentsRightAttr = [];

    /**
     * Attributes of the left payer element
     *
     * @var array
     */
    protected $payerLeftAttr = [];

    /**
     * Attributes of the right payer element
     *
     * @var array
     */
    protected $payerRightAttr = [];

    /**
     * Create a new payment slip
     *
     * @param PaymentSlipData $paymentSlipData The payment slip data.
     * @param float|null $slipPosX The optional X position of the slip.
     * @param float|null $slipPosY The optional Y position of the slip.
     */
    public function __construct(PaymentSlipData $paymentSlipData, $slipPosX = null, $slipPosY = null)
    {
        $this->paymentSlipData = $paymentSlipData;

        if (!is_null($slipPosX)) {
            $this->setSlipPosX($slipPosX);
        }
        if (!is_null($slipPosY)) {
            $this->setSlipPosY($slipPosY);
        }

        $this->setDefaults();
    }

    /**
     * Sets the common default attributes of the elements
     *
     * @return $this The current instance for a fluent interface.
     */
    protected function setDefaults()
    {
        $this->setBankLeftAttr(3, 8, 50, 4);
        $this->setBankRightAttr(66, 8, 50, 4);
        $this->setRecipientLeftAttr(3, 23, 50, 4);
        $this->setRecipientRightAttr(66, 23, 50, 4);
        $this->setAccountLeftAttr(27, 43, 30, 4);
        $this->setAccountRightAttr(90, 43, 30, 4);
        $this->setAmountFrancsLeftAttr(5, 50.5, 35, 4);
        $this->setAmountFrancsRightAttr(66, 50.5, 35, 4);
        $this->setAmountCentsLeftAttr(50, 50.5, 6, 4);
        $this->setAmountCentsRightAttr(111, 50.5, 6, 4);
        $this->setPayerLeftAttr(3, 65, 50, 4);
        $this->setPayerRightAttr(125, 48, 50, 4);

        return $this;
    }

    /**
     * Get the slip data object of the slip
     *
     * @return PaymentSlipData The data object of the slip.
     */
    public function getPaymentSlipData()
    {
        return $this->paymentSlipData;
    }

    /**
     * Set the starting X & Y position of the slip
     *
     * @param float $slipPosX The starting X position of the slip.
     * @param float $slipPosY The starting Y position of the slip
     * @return $this The current instance for a fluent interface.
     */
    public function setSlipPosition($slipPosX, $slipPosY)
    {
        $this->setSlipPosX($slipPosX);
        $this->setSlipPosY($slipPosY);

        return $this;
    }

    /**
     * Set the starting X position of the slip
     *
     * @param float $slipPosX The starting X position of the slip.
     * @return $this The current instance for a fluent interface.
     */
    protected function setSlipPosX($slipPosX)
    {
        $this->isIntOrFloat($slipPosX, 'slipPosX');
        $this->slipPosX = $slipPosX;

        return $this;
    }

    /**
     * Set the starting Y position of the slip
     *
     * @param float $slipPosY The starting Y position of the slip.
     * @return $this The current instance for a fluent interface.
     */
    protected function setSlipPosY($slipPosY)
    {
        $this->isIntOrFloat($slipPosY, '$slipPosY');
        $this->slipPosY = $slipPosY;

        return $this;
    }

    /**
     * Set the height & width of the slip
     *
     * @param float $slipWidth The width of the slip
     * @param float $slipHeight The height of the slip
     * @return $this The current instance for a fluent interface.
     */
    public function setSlipSize($slipWidth, $slipHeight)
    {
        $this->setSlipHeight($slipHeight);
        $this->setSlipWidth($slipWidth);

        return $this;
    }

    /**
     * Set the width of the slip
     *
     * @param float $slipWidth The width of the slip
     * @return $this The current instance for a fluent interface.
     */
    protected function setSlipWidth($slipWidth)
    {
        $this->isIntOrFloat($slipWidth, 'slipWidth');
        $this->slipWidth = $slipWidth;

        return $this;
    }

    /**
     * Set the height of the slip
     *
     * @param float $slipHeight The height of the slip
     * @return $this The current instance for a fluent interface.
     */
    protected function setSlipHeight($slipHeight)
    {
        $this->isIntOrFloat($slipHeight, 'slipHeight');
        $this->slipHeight = $slipHeight;

        return $this;
    }

    /**
     * Set the background of the slip
     *
     * Can be either 'transparent', a color or an image
     *
     * @param string $slipBackground The background of the slip.
     * @return $this The current instance for a fluent interface.
     *
     * @todo Implement sanity checks on parameter (filename or color)
     */
    public function setSlipBackground($slipBackground)
    {
        $this->slipBackground = $slipBackground;

        return $this;
    }

    /**
     * Set the attributes for a given payment slip element
     *
     * @param array $element The element (attributes) to set.
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
    protected function setAttributes(
        &$element,
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
        if ($posX) {
            $element['PosX'] = $posX;
        } elseif (!isset($element['PosX'])) {
            $element['PosX'] = 0;
        }
        if ($posY) {
            $element['PosY'] = $posY;
        } elseif (!isset($element['PosY'])) {
            $element['PosY'] = 0;
        }
        if ($width) {
            $element['Width'] = $width;
        } elseif (!isset($element['Width'])) {
            $element['Width'] = 0;
        }
        if ($height) {
            $element['Height'] = $height;
        } elseif (!isset($element['Height'])) {
            $element['Height'] = 0;
        }
        if (!empty($background)) {
            $element['Background'] = $background;
        } elseif (!isset($element['Background'])) {
            $element['Background'] = 'transparent';
        }
        if (!empty($fontFamily)) {
            $element['FontFamily'] = $fontFamily;
        } elseif (!isset($element['FontFamily'])) {
            $element['FontFamily'] = $this->defaultFontFamily;
        }
        if ($fontSize) {
            $element['FontSize'] = $fontSize;
        } elseif (!isset($element['FontSize'])) {
            $element['FontSize'] = $this->defaultFontSize;
        }
        if (!empty($fontColor)) {
            $element['FontColor'] = $fontColor;
        } elseif (!isset($element['FontColor'])) {
            $element['FontColor'] = $this->defaultFontColor;
        }
        if ($lineHeight) {
            $element['LineHeight'] = $lineHeight;
        } elseif (!isset($element['LineHeight'])) {
            $element['LineHeight'] = $this->defaultLineHeight;
        }
        if (!empty($textAlign)) {
            $element['TextAlign'] = $textAlign;
        } elseif (!isset($element['TextAlign'])) {
            $element['TextAlign'] = $this->defaultTextAlign;
        }

        return $this;
    }

    /**
     * Set the left bank attributes
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
    public function setBankLeftAttr(
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
            $this->bankLeftAttr,
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
     * Set the right bank attributes
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
    public function setBankRightAttr(
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
            $this->bankRightAttr,
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
     * Set the left recipient attributes
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
    public function setRecipientLeftAttr(
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
            $this->recipientLeftAttr,
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
     * Set the right recipient attributes
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
    public function setRecipientRightAttr(
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
            $this->recipientRightAttr,
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
     * Set the left account attributes
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
    public function setAccountLeftAttr(
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
            $this->accountLeftAttr,
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
     * Set the right account attributes
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
    public function setAccountRightAttr(
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
            $this->accountRightAttr,
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
     * Set the left francs amount attributes
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
    public function setAmountFrancsLeftAttr(
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
            $this->amountFrancsLeftAttr,
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
     * Set the right francs amount attributes
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
    public function setAmountFrancsRightAttr(
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
            $this->amountFrancsRightAttr,
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
     * Set the left cents amount attributes
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
    public function setAmountCentsLeftAttr(
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
            $this->amountCentsLeftAttr,
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
     * Set the right cents amount attributes
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
    public function setAmountCentsRightAttr(
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
            $this->amountCentsRightAttr,
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
     * Set the left payer attributes
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
    public function setPayerLeftAttr(
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
            $this->payerLeftAttr,
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
     * Set the right payer attributes
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
    public function setPayerRightAttr(
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
            $this->payerRightAttr,
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
     * Get the attributes of the left account element
     *
     * @return array The attributes of the left account element.
     */
    public function getAccountLeftAttr()
    {
        return $this->accountLeftAttr;
    }

    /**
     * Get the attributes of the right account element
     *
     * @return array The attributes of the right account element.
     */
    public function getAccountRightAttr()
    {
        return $this->accountRightAttr;
    }

    /**
     * Get the attributes of the right cents amount element
     *
     * @return array The attributes of the right cents amount element.
     */
    public function getAmountCentsRightAttr()
    {
        return $this->amountCentsRightAttr;
    }

    /**
     * Get the attributes of the left cents amount element
     *
     * @return array The attributes of the left cents amount element.
     */
    public function getAmountCentsLeftAttr()
    {
        return $this->amountCentsLeftAttr;
    }

    /**
     * Get the attributes of the left francs amount element
     *
     * @return array The attributes of the left francs amount element.
     */
    public function getAmountFrancsLeftAttr()
    {
        return $this->amountFrancsLeftAttr;
    }

    /**
     * Get the attributes of the right francs amount element
     *
     * @return array The attributes of the right francs amount element.
     */
    public function getAmountFrancsRightAttr()
    {
        return $this->amountFrancsRightAttr;
    }

    /**
     * Get the attributes of the left bank element
     *
     * @return array The attributes of the left bank element.
     */
    public function getBankLeftAttr()
    {
        return $this->bankLeftAttr;
    }

    /**
     * Get the attributes of the right bank element
     *
     * @return array The attributes of the right bank element.
     */
    public function getBankRightAttr()
    {
        return $this->bankRightAttr;
    }

    /**
     * Get the attributes of the right recipient element
     *
     * @return array The attributes of the right recipient element.
     */
    public function getRecipientRightAttr()
    {
        return $this->recipientRightAttr;
    }

    /**
     * Get the attributes of the left recipient element
     *
     * @return array The attributes of the left recipient element.
     */
    public function getRecipientLeftAttr()
    {
        return $this->recipientLeftAttr;
    }

    /**
     * Get the attributes of the right payer element
     *
     * @return array The attributes of the right payer element.
     */
    public function getPayerRightAttr()
    {
        return $this->payerRightAttr;
    }

    /**
     * Get the attributes of the left payer element
     *
     * @return array The attributes of the left payer element.
     */
    public function getPayerLeftAttr()
    {
        return $this->payerLeftAttr;
    }

    /**
     * Get the background of the slip
     *
     * Can be either 'transparent', a color or an image
     *
     * @return null|string The slip background.
     */
    public function getSlipBackground()
    {
        return $this->slipBackground;
    }

    /**
     * Get the starting X position of the slip
     *
     * @return int|float The starting X position of the slip.
     */
    public function getSlipPosX()
    {
        return $this->slipPosX;
    }

    /**
     * Get the starting Y position of the slip
     *
     * @return int|float The starting Y position of the slip.
     */
    public function getSlipPosY()
    {
        return $this->slipPosY;
    }

    /**
     * Get the width of the slip
     *
     * @return int|float The width of the slip.
     */
    public function getSlipWidth()
    {
        return $this->slipWidth;
    }

    /**
     * Get the height of the slip
     *
     * @return int|float The height of the slip.
     */
    public function getSlipHeight()
    {
        return $this->slipHeight;
    }

    /**
     * Set whether or not to display the background
     *
     * @param bool $displayBackground True if yes, false if no.
     * @return $this The current instance for a fluent interface..
     */
    public function setDisplayBackground($displayBackground = true)
    {
        $this->isBool($displayBackground, 'displayBackground');
        $this->displayBackground = $displayBackground;

        return $this;
    }

    /**
     * Get whether or not to display the background
     *
     * @return bool True if yes, false if no.
     */
    public function getDisplayBackground()
    {
        return $this->displayBackground;
    }

    /**
     * Set whether or not to display the account
     *
     * @param bool $displayAccount True if yes, false if no.
     * @return $this The current instance for a fluent interface..
     */
    public function setDisplayAccount($displayAccount = true)
    {
        $this->isBool($displayAccount, 'displayAccount');
        $this->displayAccount = $displayAccount;

        return $this;
    }

    /**
     * Get whether or not to display the account
     *
     * @return bool True if yes, false if no.
     */
    public function getDisplayAccount()
    {
        if ($this->getPaymentSlipData()->getWithAccountNumber() !== true) {
            return false;
        }
        return $this->displayAccount;
    }

    /**
     * Set whether or not to display the amount
     *
     * @param bool $displayAmount True if yes, false if no
     * @return $this The current instance for a fluent interface.
     */
    public function setDisplayAmount($displayAmount = true)
    {
        $this->isBool($displayAmount, 'displayAmount');
        $this->displayAmount = $displayAmount;

        return $this;
    }

    /**
     * Get whether or not to display the amount
     *
     * @return bool True if yes, false if no.
     */
    public function getDisplayAmount()
    {
        if ($this->getPaymentSlipData()->getWithAmount() !== true) {
            return false;
        }
        return $this->displayAmount;
    }

    /**
     * Set whether or not to display the bank
     *
     * @param bool $displayBank True if yes, false if no
     * @return $this The current instance for a fluent interface.
     */
    public function setDisplayBank($displayBank = true)
    {
        $this->isBool($displayBank, 'displayBank');
        $this->displayBank = $displayBank;

        return $this;
    }

    /**
     * Get whether or not to display the bank
     *
     * @return bool True if yes, false if no.
     */
    public function getDisplayBank()
    {
        if ($this->getPaymentSlipData()->getWithBank() !== true) {
            return false;
        }
        return $this->displayBank;
    }

    /**
     * Set whether or not to display the payer
     *
     * @param bool $displayPayer True if yes, false if no
     * @return $this The current instance for a fluent interface.
     */
    public function setDisplayPayer($displayPayer = true)
    {
        $this->isBool($displayPayer, 'displayPayer');
        $this->displayPayer = $displayPayer;

        return $this;
    }

    /**
     * Get whether or not to display the payer
     *
     * @return bool True if yes, false if no.
     */
    public function getDisplayPayer()
    {
        if ($this->getPaymentSlipData()->getWithPayer() !== true) {
            return false;
        }
        return $this->displayPayer;
    }

    /**
     * Set whether or not to display the recipient
     *
     * @param bool $displayRecipient True if yes, false if no
     * @return $this The current instance for a fluent interface.
     */
    public function setDisplayRecipient($displayRecipient = true)
    {
        $this->isBool($displayRecipient, 'displayRecipient');
        $this->displayRecipient = $displayRecipient;

        return $this;
    }

    /**
     * Get whether or not to display the recipient
     *
     * @return bool True if yes, false if no.
     */
    public function getDisplayRecipient()
    {
        if ($this->getPaymentSlipData()->getWithRecipient() !== true) {
            return false;
        }
        return $this->displayRecipient;
    }

    /**
     * Get all elements of the slip
     *
     * @return array All elements with their lines and attributes.
     */
    public function getAllElements()
    {
        $paymentSlipData = $this->paymentSlipData;

        $elements = [];
        // Place left bank lines
        if ($this->getDisplayBank()) {
            $lines = [
                $paymentSlipData->getBankName(),
                $paymentSlipData->getBankCity()
            ];
            $elements['bankLeft'] = [
                'lines' => $lines,
                'attributes' => $this->getBankLeftAttr()
            ];

            // Place right bank lines
            // Reuse lines from above
            $elements['bankRight'] = [
                'lines' => $lines,
                'attributes' => $this->getBankRightAttr()
            ];
        }

        // Place left recipient lines
        if ($this->getDisplayRecipient()) {
            $lines = [
                $paymentSlipData->getRecipientLine1(),
                $paymentSlipData->getRecipientLine2(),
                $paymentSlipData->getRecipientLine3(),
                $paymentSlipData->getRecipientLine4()
            ];
            $elements['recipientLeft'] = [
                'lines' => $lines,
                'attributes' => $this->getRecipientLeftAttr()
            ];

            // Place right recipient lines
            // Reuse lines from above
            $elements['recipientRight'] = [
                'lines' => $lines,
                'attributes' => $this->getRecipientRightAttr()
            ];
        }

        // Place left account number
        if ($this->getDisplayAccount()) {
            $lines = [$paymentSlipData->getAccountNumber()];
            $elements['accountLeft'] = [
                'lines' => $lines,
                'attributes' => $this->getAccountLeftAttr()
            ];

            // Place right account number
            // Reuse lines from above
            $elements['accountRight'] = [
                'lines' => $lines,
                'attributes' => $this->getAccountRightAttr()
            ];
        }

        // Place left amount in francs
        if ($this->getDisplayAmount()) {
            $lines = [$paymentSlipData->getAmountFrancs()];
            $elements['amountFrancsLeft'] = [
                'lines' => $lines,
                'attributes' => $this->getAmountFrancsLeftAttr()
            ];

            // Place right amount in francs
            // Reuse lines from above
            $elements['amountFrancsRight'] = [
                'lines' => $lines,
                'attributes' => $this->getAmountFrancsRightAttr()
            ];

            // Place left amount in cents
            $lines = [$paymentSlipData->getAmountCents()];
            $elements['amountCentsLeft'] = [
                'lines' => $lines,
                'attributes' => $this->getAmountCentsLeftAttr()
            ];

            // Place right amount in cents
            // Reuse lines from above
            $elements['amountCentsRight'] = [
                'lines' => $lines,
                'attributes' => $this->getAmountCentsRightAttr()
            ];
        }

        // Place left payer lines
        if ($this->getDisplayPayer()) {
            $lines = [
                $paymentSlipData->getPayerLine1(),
                $paymentSlipData->getPayerLine2(),
                $paymentSlipData->getPayerLine3(),
                $paymentSlipData->getPayerLine4()
            ];
            $elements['payerLeft'] = [
                'lines' => $lines,
                'attributes' => $this->getPayerLeftAttr()
            ];

            // Place right payer lines
            // Reuse lines from above
            $elements['payerRight'] = [
                'lines' => $lines,
                'attributes' => $this->getPayerRightAttr()
            ];
        }

        return $elements;
    }

    /**
     * Verify that a given parameter is an integer or a float
     *
     * @param mixed $parameter The given parameter to validate.
     * @param string $varName The name of the variable.
     * @return true If the parameter is either  an integer or a float.
     * @throws InvalidArgumentException If the parameter is neither an integer nor a float.
     */
    protected function isIntOrFloat($parameter, $varName)
    {
        if ((!is_int($parameter) && !is_float($parameter))) {
            throw new InvalidArgumentException(
                sprintf(
                    '$%s is neither an integer nor a float.',
                    $varName
                )
            );
        }
    }

    /**
     * Verify that a given parameter is boolean
     *
     * @param mixed $parameter The given parameter to validate.
     * @param string $varName The name of the variable.
     * @return true If the parameter is a boolean.
     * @throws InvalidArgumentException If the parameter is not a boolean.
     */
    protected function isBool($parameter, $varName)
    {
        if (!is_bool($parameter)) {
            throw new InvalidArgumentException(
                sprintf(
                    '$%s is not a boolean.',
                    $varName
                )
            );
        }
        return true;
    }
}
