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

namespace SwissPaymentSlip\SwissPaymentSlip\Exception;

/**
 * Exception when requesting disabled data
 */
class DisabledDataException extends PaymentSlipException
{
    /**
     * The name of the requested but disabled data
     *
     * @var null|string
     */
    protected $dataName = null;

    /**
     * Construct the message using the given data name
     *
     * @param string $dataName
     */
    public function __construct($dataName)
    {
        $this->dataName = $dataName;

        $message = sprintf(
            'You are accessing the disabled %s. You need to re-enable it first.',
            $dataName
        );

        parent::__construct($message);
    }

    /**
     * Get the name of the requested but disabled data
     *
     * @return null|string The name of the requested but disabled data.
     */
    public function getDataName()
    {
        return $this->dataName;
    }
}
