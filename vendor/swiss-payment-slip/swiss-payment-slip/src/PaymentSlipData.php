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
use SwissPaymentSlip\SwissPaymentSlip\Exception\PaymentSlipException;
use SwissPaymentSlip\SwissPaymentSlip\Exception\DisabledDataException;

/**
 * Swiss Payment Slip Data
 *
 * A base class for all Swiss payment slip data classes,
 * which encapsulate all the common data
 * of the various Swiss payment slips types.
 *
 * It actually doesn't do much. It's mostly a data container class to keep
 * including classes from having to care about how a particular slip actually works.
 *
 * But it provides a flexibility of which data it holds, because not always
 * all slip fields are needed in an application.
 *
 * Glossary:
 * ESR = Einzahlungsschein mit Referenznummer
 *     ISR, (In-)Payment slip with reference number
 *     Summary term for orange payment slips in Switzerland
 * BESR = Banken-Einzahlungsschein mit Referenznummer
 *     Banking payment slip with reference number
 *     Orange payment slip for paying into a bank account (in contrast to a post cheque account with a VESR)
 * VESR = Verfahren für Einzahlungsschein mit Referenznummer
 *     Procedure for payment slip with reference number
 *     Orange payment slip for paying into a post cheque account (in contrast to a banking account with a BESR)
 * (B|V)ESR+ = Einzahlungsschein mit Referenznummer ohne Betragsangabe
 *     Payment slip with reference number without amount specification
 *     An payment slip can be issued without a predefined payment amount
 * ES = Einzahlungsschein
 *     IS, (In-)Payment slip
 *     Also summary term for all payment slips.
 *     Red payment slip for paying into a post cheque or bank account without reference number, with message box
 *
 * @link http://www.six-interbank-clearing.com/en/home/standardization/dta.html Payments in DTA format
 *
 * @todo Implement currency (CHF, EUR), means different prefixes in code line
 * @todo Implement payment on own account, means different prefixes in code line --> edge case!
 * @todo Implement cash on delivery (Nachnahme), means different prefixes in code line --> do it on demand
 * @todo Implement amount check for unrounded (.05) cents, document why (see manual)
 * @todo Create a getBankData method with formatting parameter, e.g. stripping blank lines
 * @todo Create a getRecipientData with formatting parameter, e.g. stripping blank lines
 */
abstract class PaymentSlipData
{

    /**
     * The array table for calculating the check digit by modulo 10
     *
     * @var array
     */
    private $moduloTable = [0, 9, 4, 6, 8, 2, 7, 1, 3, 5];

    /**
     * Determines if the payment slip has a recipient bank. Can be disabled for pre-printed payment slips
     *
     * @var bool
     */
    protected $withBank = true;

    /**
     * Determines if the payment slip has a account number. Can be disabled for pre-printed payment slips
     *
     * @var bool
     */
    protected $withAccountNumber = true;

    /**
     * Determines if the payment slip has a recipient. Can be disabled for pre-printed payment slips
     *
     * @var bool
     */
    protected $withRecipient = true;

    /**
     * Determines if it's an ESR or an ESR+
     *
     * @var bool
     */
    protected $withAmount = true;

    /**
     * Determines if the payment slip has a payer. Can be disabled for pre-printed payment slips
     *
     * @var bool
     */
    protected $withPayer = true;

    /**
     * The name of the bank
     *
     * @var string
     */
    protected $bankName = '';

    /**
     * The postal code and city of the bank
     *
     * @var string
     */
    protected $bankCity = '';

    /**
     * The bank or post cheque account where the money will be transferred to
     *
     * @var string
     */
    protected $accountNumber = '';

    /**
     * The first line of the recipient, e.g. "My Company Ltd."
     *
     * @var string
     */
    protected $recipientLine1 = '';

    /**
     * The second line of the recipient, e.g. "Examplestreet 61"
     *
     * @var string
     */
    protected $recipientLine2 = '';

    /**
     * The third line of the recipient, e.g. "8000 Zürich"
     *
     * @var string
     */
    protected $recipientLine3 = '';

    /**
     * The fourth line of the recipient, if needed
     *
     * @var string
     */
    protected $recipientLine4 = '';

    /**
     * The amount to be payed into. Can be disabled with withAmount = false for ESR+ slips
     *
     * @var float
     */
    protected $amount = 0.0;

    /**
     * The first line of the payer, e.g. "Hans Mustermann"
     *
     * @var string
     */
    protected $payerLine1 = '';

    /**
     * The second line of the payer, e.g. "Main Street 11"
     *
     * @var string
     */
    protected $payerLine2 = '';

    /**
     * The third line of the payer, e.g. "4052 Basel"
     *
     * @var string
     */
    protected $payerLine3 = '';

    /**
     * The fourth line of the payer, if needed
     *
     * @var string
     */
    protected $payerLine4 = '';

    /**
     * Determines if the payment slip must not be used for payment (XXXed out)
     *
     * @var bool
     */
    protected $notForPayment = false;

    /**
     * Set if payment slip has a bank specified
     *
     * Resets the bank data when disabling.
     *
     * @param bool $withBank True for yes, false for no
     * @return $this The current instance for a fluent interface.
     */
    public function setWithBank($withBank = true)
    {
        $this->isBool($withBank, 'withBank');
        $this->withBank = $withBank;

        if ($withBank === false) {
            $this->bankName = '';
            $this->bankCity = '';
        }

        return $this;
    }

    /**
     * Get if payment slip has recipient specified
     *
     * @return bool True if payment slip has the recipient specified, else false.
     */
    public function getWithBank()
    {
        return $this->withBank;
    }

    /**
     * Set if payment slip has an account number specified
     *
     * Resets the account number when disabling.
     *
     * @param bool $withAccountNumber True if yes, false if no.
     * @return $this The current instance for a fluent interface.
     */
    public function setWithAccountNumber($withAccountNumber = true)
    {
        $this->isBool($withAccountNumber, 'withAccountNumber');
        $this->withAccountNumber = $withAccountNumber;

        if ($withAccountNumber === false) {
            $this->accountNumber = '';
        }

        return $this;
    }

    /**
     * Get if payment slip has an account number specified
     *
     * @return bool True if payment slip has an account number specified, else false.
     */
    public function getWithAccountNumber()
    {
        return $this->withAccountNumber;
    }

    /**
     * Set if payment slip has a recipient specified
     *
     * Resets the recipient data when disabling.
     *
     * @param bool $withRecipient True if yes, false if no.
     * @return $this The current instance for a fluent interface.
     */
    public function setWithRecipient($withRecipient = true)
    {
        $this->isBool($withRecipient, 'withRecipient');
        $this->withRecipient = $withRecipient;

        if ($withRecipient === false) {
            $this->recipientLine1 = '';
            $this->recipientLine2 = '';
            $this->recipientLine3 = '';
            $this->recipientLine4 = '';
        }

        return $this;
    }

    /**
     * Get if payment slip has a recipient specified
     *
     * @return bool True if payment slip has a recipient specified, else false.
     */
    public function getWithRecipient()
    {
        return $this->withRecipient;
    }

    /**
     * Set if payment slip has an amount specified
     *
     * Resets the amount when disabling.
     *
     * @param bool $withAmount True for yes, false for no.
     * @return $this The current instance for a fluent interface.
     */
    public function setWithAmount($withAmount = true)
    {
        $this->isBool($withAmount, 'withAmount');
        $this->withAmount = $withAmount;

        if ($withAmount === false) {
            $this->amount = 0.0;
        }

        return $this;
    }

    /**
     * Get if payment slip has an amount specified
     *
     * @return bool True if payment slip has an amount specified, else false.
     */
    public function getWithAmount()
    {
        return $this->withAmount;
    }

    /**
     * Set if payment slip has a payer specified
     *
     * Resets the payer data when disabling.
     *
     * @param bool $withPayer True if yes, false if no.
     * @return $this The current instance for a fluent interface.
     */
    public function setWithPayer($withPayer = true)
    {
        $this->isBool($withPayer, 'withPayer');
        $this->withPayer = $withPayer;

        if ($withPayer === false) {
            $this->payerLine1 = '';
            $this->payerLine2 = '';
            $this->payerLine3 = '';
            $this->payerLine4 = '';
        }

        return $this;
    }

    /**
     * Get if payment slip has a payer specified
     *
     * @return bool True if payment slip has a payer specified, else false.
     */
    public function getWithPayer()
    {
        return $this->withPayer;
    }

    /**
     * Sets the name, city and account number of the bank
     *
     * @param string $bankName Name of the bank.
     * @param string $bankCity City of the bank.
     * @return $this The current instance for a fluent interface.
     */
    public function setBankData($bankName, $bankCity)
    {
        $this->setBankName($bankName);
        $this->setBankCity($bankCity);

        return $this;
    }

    /**
     * Set the name of the bank
     *
     * @param string $bankName The name of the bank.
     * @return $this The current instance for a fluent interface.
     * @throws DisabledDataException If the data is disabled.
     *
     * @todo Implement max length check
     */
    public function setBankName($bankName)
    {
        if (!$this->getWithBank()) {
            throw new DisabledDataException('bank name');
        }
        $this->bankName = $bankName;

        return $this;
    }

    /**
     * Get the name of the bank
     *
     * @return string The name of the bank, if withBank is set to true.
     * @throws DisabledDataException If the data is disabled.
     */
    public function getBankName()
    {
        if (!$this->getWithBank()) {
            throw new DisabledDataException('bank name');
        }
        return $this->bankName;
    }

    /**
     * Set the postal code and city of the bank
     *
     * @param string $bankCity The postal code and city of the bank
     * @return $this The current instance for a fluent interface.
     * @throws DisabledDataException If the data is disabled.
     *
     * @todo Implement max length check
     */
    public function setBankCity($bankCity)
    {
        if (!$this->getWithBank()) {
            throw new DisabledDataException('bank city');
        }
        $this->bankCity = $bankCity;

        return $this;
    }

    /**
     * Get the postal code and city of the bank
     *
     * @return string The postal code and city, if withBank is set to true.
     * @throws DisabledDataException If the data is disabled.
     */
    public function getBankCity()
    {
        if (!$this->getWithBank()) {
            throw new DisabledDataException('bank city');
        }
        return $this->bankCity;
    }

    /**
     * Set the bank or post cheque account where the money will be transferred to
     *
     * @param string $accountNumber The bank or post cheque account.
     * @return $this The current instance for a fluent interface.
     * @throws DisabledDataException If the data is disabled.
     *
     * @todo Implement parameter validation (two hyphens, min & max length)
     */
    public function setAccountNumber($accountNumber)
    {
        if (!$this->getWithAccountNumber()) {
            throw new DisabledDataException('account number');
        }
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * Get the bank or post cheque account where the money will be transferred to
     *
     * @return string The bank or post cheque account, if withAccountNumber is set to true.
     * @throws DisabledDataException If the data is disabled.
     */
    public function getAccountNumber()
    {
        if (!$this->getWithAccountNumber()) {
            throw new DisabledDataException('account number');
        }
        return $this->accountNumber;
    }

    /**
     * Sets the four lines of the recipient
     *
     * @param string $recipientLine1 The first line of the recipient, e.g. "My Company Ltd.".
     * @param string $recipientLine2 The second line of the recipient, e.g. "Examplestreet 61".
     * @param string $recipientLine3 The third line of the recipient, e.g. "8000 Zürich".
     * @param string $recipientLine4 The fourth line of the recipient, if needed.
     * @return $this The current instance for a fluent interface.
     */
    public function setRecipientData($recipientLine1, $recipientLine2, $recipientLine3 = '', $recipientLine4 = '')
    {
        $this->setRecipientLine1($recipientLine1);
        $this->setRecipientLine2($recipientLine2);
        $this->setRecipientLine3($recipientLine3);
        $this->setRecipientLine4($recipientLine4);

        return $this;
    }

    /**
     * Set the first line of the recipient
     *
     * @param string $recipientLine1 The first line of the recipient, e.g. "My Company Ltd.".
     * @return $this The current instance for a fluent interface.
     * @throws DisabledDataException If the data is disabled.
     */
    public function setRecipientLine1($recipientLine1)
    {
        if (!$this->getWithRecipient()) {
            throw new DisabledDataException('recipient line 1');
        }
        $this->recipientLine1 = $recipientLine1;

        return $this;
    }

    /**
     * Get the first line of the recipient
     *
     * @return string The first line of the recipient, if withRecipient is set to true.
     * @throws DisabledDataException If the data is disabled.
     */
    public function getRecipientLine1()
    {
        if (!$this->getWithRecipient()) {
            throw new DisabledDataException('recipient line 1');
        }
        return $this->recipientLine1;
    }

    /**
     * Set the second line of the recipient
     *
     * @param string $recipientLine2 The second line of the recipient, e.g. "Examplestreet 61".
     * @return $this The current instance for a fluent interface.
     * @throws DisabledDataException If the data is disabled.
     */
    public function setRecipientLine2($recipientLine2)
    {
        if (!$this->getWithRecipient()) {
            throw new DisabledDataException('recipient line 2');
        }
        $this->recipientLine2 = $recipientLine2;

        return $this;
    }

    /**
     * Get the second line of the recipient
     *
     * @return string The second line of the recipient, if withRecipient is set to true.
     * @throws DisabledDataException If the data is disabled.
     */
    public function getRecipientLine2()
    {
        if (!$this->getWithRecipient()) {
            throw new DisabledDataException('recipient line 2');
        }
        return $this->recipientLine2;
    }

    /**
     * Set the third line of the recipient
     *
     * @param string $recipientLine3 The third line of the recipient, e.g. "8000 Zürich".
     * @return $this The current instance for a fluent interface.
     * @throws DisabledDataException If the data is disabled.
     */
    public function setRecipientLine3($recipientLine3)
    {
        if (!$this->getWithRecipient()) {
            throw new DisabledDataException('recipient line 3');
        }
        $this->recipientLine3 = $recipientLine3;

        return $this;
    }

    /**
     * Get the third line of the recipient
     *
     * @return string The third line of the recipient, if withRecipient is set to true.
     * @throws DisabledDataException If the data is disabled.
     */
    public function getRecipientLine3()
    {
        if (!$this->getWithRecipient()) {
            throw new DisabledDataException('recipient line 3');
        }
        return $this->recipientLine3;
    }

    /**
     * Set the fourth line of the recipient
     *
     * @param string $recipientLine4 The fourth line of the recipient, if needed.
     * @return $this The current instance for a fluent interface.
     * @throws DisabledDataException If the data is disabled.
     */
    public function setRecipientLine4($recipientLine4)
    {
        if (!$this->getWithRecipient()) {
            throw new DisabledDataException('recipient line 4');
        }
        $this->recipientLine4 = $recipientLine4;

        return $this;
    }

    /**
     * Get the fourth line of the recipient
     *
     * @return string The fourth line of the recipient, if withRecipient is set to true.
     * @throws DisabledDataException If the data is disabled.
     */
    public function getRecipientLine4()
    {
        if (!$this->getWithRecipient()) {
            throw new DisabledDataException('recipient line 4');
        }
        return $this->recipientLine4;
    }

    /**
     * Set the amount of the payment slip. Only possible if it's not a ESR+.
     *
     * @param float $amount The amount to be payed into
     * @return $this The current instance for a fluent interface.
     * @throws DisabledDataException If the data is disabled.
     */
    public function setAmount($amount = 0.0)
    {
        if (!$this->getWithAmount()) {
            throw new DisabledDataException('amount');
        }
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get the amount to be payed into
     *
     * @return float The amount to be payed into.
     * @throws DisabledDataException If the data is disabled.
     */
    public function getAmount()
    {
        if (!$this->getWithAmount()) {
            throw new DisabledDataException('amount');
        }
        return $this->amount;
    }

    /**
     * Sets the four lines of the payer
     *
     * At least two lines are necessary.
     *
     * @param string $payerLine1 The first line of the payer, e.g. "Hans Mustermann".
     * @param string $payerLine2 The second line of the payer, e.g. "Main Street 11".
     * @param string $payerLine3 The third line of the payer, e.g. "4052 Basel".
     * @param string $payerLine4 The fourth line of the payer, if needed.
     * @return $this The current instance for a fluent interface.
     */
    public function setPayerData($payerLine1, $payerLine2, $payerLine3 = '', $payerLine4 = '')
    {
        $this->setPayerLine1($payerLine1);
        $this->setPayerLine2($payerLine2);
        $this->setPayerLine3($payerLine3);
        $this->setPayerLine4($payerLine4);

        return $this;
    }

    /**
     * Set the first line of the payer
     *
     * @param string $payerLine1 The first line of the payer, e.g. "Hans Mustermann".
     * @return $this The current instance for a fluent interface.
     * @throws DisabledDataException If the data is disabled.
     */
    public function setPayerLine1($payerLine1)
    {
        if (!$this->getWithPayer()) {
            throw new DisabledDataException('payer line 1');
        }
        $this->payerLine1 = $payerLine1;

        return $this;
    }

    /**
     * Get the first line of the payer
     *
     * @return string The first line of the payer, if withPayer is set to true.
     * @throws DisabledDataException If the data is disabled.
     */
    public function getPayerLine1()
    {
        if (!$this->getWithPayer()) {
            throw new DisabledDataException('payer line 1');
        }
        return $this->payerLine1;
    }

    /**
     * Set the second line of the payer
     *
     * @param string $payerLine2 The second line of the payer, e.g. "Main Street 11".
     * @return $this The current instance for a fluent interface.
     * @throws DisabledDataException If the data is disabled.
     */
    public function setPayerLine2($payerLine2)
    {
        if (!$this->getWithPayer()) {
            throw new DisabledDataException('payer line 2');
        }
        $this->payerLine2 = $payerLine2;

        return $this;
    }

    /**
     * Get the second line of the payer
     *
     * @return string The second line of the payer, if withPayer is set to true.
     * @throws DisabledDataException If the data is disabled.
     */
    public function getPayerLine2()
    {
        if (!$this->getWithPayer()) {
            throw new DisabledDataException('payer line 2');
        }
        return $this->payerLine2;
    }

    /**
     * Set the third line of the payer
     *
     * @param string $payerLine3 The third line of the payer, e.g. "4052 Basel".
     * @return $this The current instance for a fluent interface.
     * @throws DisabledDataException If the data is disabled.
     */
    public function setPayerLine3($payerLine3)
    {
        if (!$this->getWithPayer()) {
            throw new DisabledDataException('payer line 3');
        }
        $this->payerLine3 = $payerLine3;

        return $this;
    }

    /**
     * Get the third line of the payer
     *
     * @return string The third line of the payer, if withPayer is set to true.
     * @throws DisabledDataException If the data is disabled.
     */
    public function getPayerLine3()
    {
        if (!$this->getWithPayer()) {
            throw new DisabledDataException('payer line 3');
        }
        return $this->payerLine3;
    }

    /**
     * Set the fourth line of the payer
     *
     * @param string $payerLine4 The fourth line of the payer, if needed.
     * @return $this The current instance for a fluent interface.
     * @throws DisabledDataException If the data is disabled.
     */
    public function setPayerLine4($payerLine4)
    {
        if (!$this->getWithPayer()) {
            throw new DisabledDataException('payer line 4');
        }
        $this->payerLine4 = $payerLine4;

        return $this;
    }

    /**
     * Get the fourth line of the payer
     *
     * @return string The fourth line of the payer, if withPayer is set to true.
     * @throws DisabledDataException If the data is disabled.
     */
    public function getPayerLine4()
    {
        if (!$this->getWithPayer()) {
            throw new DisabledDataException('payer line 4');
        }
        return $this->payerLine4;
    }

    /**
     * Clear the account of the two hyphens
     *
     * @return string The account of the two hyphens, 'XXXXXXXXX' if not for payment or else false.
     * @throws DisabledDataException If the data is disabled.
     * @throws PaymentSlipException If account number does not contain two hyphens.
     * @todo Cover the edge cases with tests
     */
    protected function getAccountDigits()
    {
        if (!$this->getWithAccountNumber()) {
            throw new DisabledDataException('account number');
        }
        if ($this->getNotForPayment()) {
            return 'XXXXXXXXX';
        }
        $accountNumber = $this->getAccountNumber();
        if ($accountNumber === '') {
            return $accountNumber;
        }
        $accountDigits = str_replace('-', '', $accountNumber, $replacedHyphens);
        if ($replacedHyphens != 2) {
            throw new PaymentSlipException('Invalid Account number. Does not contain two hyphens.');
        }
        return $accountDigits;
    }

    /**
     * Get the francs amount without cents
     *
     * @return bool|int Francs amount without cents.
     */
    public function getAmountFrancs()
    {
        if ($this->getNotForPayment()) {
            return 'XXXXXXXX';
        }
        $amount = $this->getAmount();
        $francs = intval($amount);
        return $francs;
    }

    /**
     * Get the zero filled, right padded, two digits long cents amount
     *
     * @return bool|string Amount of Cents, zero filled, right padded, two digits long.
     */
    public function getAmountCents()
    {
        if ($this->getNotForPayment()) {
            return 'XX';
        }
        $amount = $this->getAmount();
        $francs = intval($amount);
        $cents = round(($amount - $francs) * 100);
        return str_pad($cents, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Set payment slip for not to be used for payment
     *
     * XXXes out all fields to prevent people using the payment slip.
     *
     * @param boolean $notForPayment True if not for payment, else false.
     * @return $this The current instance for a fluent interface.
     */
    public function setNotForPayment($notForPayment = true)
    {
        $this->notForPayment = $notForPayment;

        if ($notForPayment === true) {
            if ($this->getWithBank() === true) {
                $this->setBankData('XXXXXX', 'XXXXXX');
            }
            if ($this->getWithAccountNumber() === true) {
                $this->setAccountNumber('XXXXXX');
            }
            if ($this->getWithRecipient() === true) {
                $this->setRecipientData('XXXXXX', 'XXXXXX', 'XXXXXX', 'XXXXXX');
            }
            if ($this->getWithPayer() === true) {
                $this->setPayerData('XXXXXX', 'XXXXXX', 'XXXXXX', 'XXXXXX');
            }
            if ($this->getWithAmount() === true) {
                $this->setAmount('XXXXXXXX.XX');
            }
        }

        return $this;
    }

    /**
     * Get whether this payment slip must not be used for payment
     *
     * @return bool True if yes, else false.
     */
    public function getNotForPayment()
    {
        return $this->notForPayment;
    }

    /**
     * Creates Modulo10 recursive check digit
     *
     * @copyright As found on http://www.developers-guide.net/forums/5431,modulo10-rekursiv (thanks, dude!)
     * @param string $number Number to create recursive check digit off.
     * @return int Recursive check digit.
     */
    protected function modulo10($number)
    {
        $next = 0;
        for ($i=0; $i < strlen($number); $i++) {
            $next = $this->moduloTable[($next + intval(substr($number, $i, 1))) % 10];
        }

        return (10 - $next) % 10;
    }

    /**
     * Get a given string broken down in blocks of a certain size
     *
     * Example: 000000000000000 becomes more readable 00000 00000 00000
     *
     * @param string $string The to be formatted string.
     * @param int $blockSize The Block size of choice.
     * @param bool $alignFromRight Right aligned, blocks are build from right.
     * @return string Given string divided in blocks of given block size separated by one space.
     */
    protected function breakStringIntoBlocks($string, $blockSize = 5, $alignFromRight = true)
    {
        // Lets reverse the string (because we want the block to be aligned from the right)
        if ($alignFromRight === true) {
            $string = strrev($string);
        }

        // Chop it into blocks
        $string = trim(chunk_split($string, $blockSize, ' '));

        // Re-reverse
        if ($alignFromRight === true) {
            $string = strrev($string);
        }

        return $string;
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
