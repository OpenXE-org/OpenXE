<?php
namespace Fhp\Dialog;

use Fhp\Adapter\Exception\AdapterException;
use Fhp\Adapter\Exception\CurlException;
use Fhp\Connection;
use Fhp\Dialog\Exception\FailedRequestException;
use Fhp\Message\AbstractMessage;
use Fhp\Message\Message;
use Fhp\Response\Initialization;
use Fhp\Response\Response;
use Fhp\Segment\HKEND;
use Fhp\Segment\HKIDN;
use Fhp\Segment\HKSYN;
use Fhp\Segment\HKVVB;

/**
 * Class Dialog
 * @package Fhp\Dialog
 */
class Dialog
{
    const DEFAULT_COUNTRY_CODE = 280;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var int
     */
    protected $messageNumber = 1;

    /**
     * @var int
     */
    protected $dialogId = 0;

    /**
     * @var int|string
     */
    protected $systemId = 0;

    /**
     * @var string
     */
    protected $bankCode;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $pin;

    /**
     * @var string
     */
    protected $bankName;

    /**
     * @var array
     */
    protected $supportedTanMechanisms = array();

    /**
     * @var int
     */
    protected $hksalVersion = 6;

    /**
     * @var int
     */
    protected $hkkazVersion = 6;

    /**
     * Dialog constructor.
     *
     * @param Connection $connection
     * @param string $bankCode
     * @param string $username
     * @param string $pin
     * @param string $systemId
     */
    public function __construct(Connection $connection, $bankCode, $username, $pin, $systemId)
    {
        $this->connection = $connection;
        $this->bankCode = $bankCode;
        $this->username = $username;
        $this->pin = $pin;
        $this->systemId = $systemId;
    }

    /**
     * @param AbstractMessage $message
     * @return Response
     * @throws AdapterException
     * @throws CurlException
     * @throws FailedRequestException
     */
    public function sendMessage(AbstractMessage $message)
    {
        try {
            $message->setMessageNumber($this->messageNumber);
            $message->setDialogId($this->dialogId);

            $result = $this->connection->send($message);
            $this->messageNumber++;
            $response = new Response($result);

            $this->handleResponse($response);

            if (!$response->isSuccess()) {
                $summary = $response->getMessageSummary();
                $ex = new FailedRequestException($summary);
                throw $ex;
            }

            return $response;
        } catch (AdapterException $e) {
            if ($e instanceof CurlException) {
            }

            throw $e;
        }
    }

    /**
     * @param Response $response
     */
    protected function handleResponse(Response $response)
    {
        $summary = $response->getMessageSummary();
        $segSum  = $response->getSegmentSummary();

        foreach ($summary as $code => $message) {
            $this->logMessage('HIRMG', $code, $message);
        }

        foreach ($segSum as $code => $message) {
            $this->logMessage('HIRMS', $code, $message);
        }
    }

    /**
     * @param string $type
     * @param string $code
     * @param $message
     */
    protected function logMessage($type, $code, $message)
    {
    }

    /**
     * Gets the dialog ID.
     *
     * @return integer
     */
    public function getDialogId()
    {
        return $this->dialogId;
    }

    /**
     * Gets the current message number.
     *
     * @return int
     */
    public function getMessageNumber()
    {
        return $this->messageNumber;
    }

    /**
     * Gets the system ID.
     *
     * @return int|string
     */
    public function getSystemId()
    {
        return $this->systemId;
    }

    /**
     * Gets all supported TAN mechanisms.
     *
     * @return array
     */
    public function getSupportedPinTanMechanisms()
    {
        return $this->supportedTanMechanisms;
    }

    /**
     * Gets the max possible HKSAL version.
     *
     * @return int
     */
    public function getHksalMaxVersion()
    {
        return $this->hksalVersion;
    }

    /**
     * Gets the max possible HKKAZ version.
     *
     * @return int
     */
    public function getHkkazMaxVersion()
    {
        return $this->hkkazVersion;
    }

    /**
     * Gets the bank name.
     *
     * @return string
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * Initializes a dialog.
     *
     * @return string|null
     * @throws AdapterException
     * @throws CurlException
     * @throws FailedRequestException
     * @throws \Exception
     */
    public function initDialog()
    {
        $identification = new HKIDN(3, $this->bankCode, $this->username, $this->systemId);
        $prepare        = new HKVVB(4, HKVVB::DEFAULT_BPD_VERSION, HKVVB::DEFAULT_UPD_VERSION, HKVVB::LANG_DEFAULT);

        $message = new Message(
            $this->bankCode,
            $this->username,
            $this->pin,
            $this->systemId,
            0,
            1,
            array($identification, $prepare),
            array(AbstractMessage::OPT_PINTAN_MECH => $this->supportedTanMechanisms)
        );


        $response = $this->sendMessage($message)->rawResponse;

        $result = new Initialization($response);
        $this->dialogId = $result->getDialogId();

        return $this->dialogId;
    }

    /**
     * Sends sync request.
     *
     * @return string
     * @throws AdapterException
     * @throws CurlException
     * @throws FailedRequestException
     * @throws \Exception
     */
    public function syncDialog()
    {
        $this->messageNumber = 1;
        $this->systemId = 0;
        $this->dialogId = 0;

        $identification = new HKIDN(3, $this->bankCode, $this->username, 0);
        $prepare        = new HKVVB(4, HKVVB::DEFAULT_BPD_VERSION, HKVVB::DEFAULT_UPD_VERSION, HKVVB::LANG_DEFAULT);
        $sync           = new HKSYN(5);

        $syncMsg = new Message(
            $this->bankCode,
            $this->username,
            $this->pin,
            $this->systemId,
            $this->dialogId,
            $this->messageNumber,
            array($identification, $prepare, $sync)
        );

        $response = $this->sendMessage($syncMsg);

        // save BPD (Bank Parameter Daten)
        $this->systemId = $response->getSystemId();
        $this->dialogId = $response->getDialogId();
        $this->bankName = $response->getBankName();

        // max version for segment HKSAL (Saldo abfragen)
        $this->hksalVersion = $response->getHksalMaxVersion();
        $this->supportedTanMechanisms = $response->getSupportedTanMechanisms();

        // max version for segment HKKAZ (Kontoumsätze anfordern / Zeitraum)
        $this->hkkazVersion = $response->getHkkazMaxVersion();

        $this->endDialog();

        return $response->rawResponse;
    }

    /**
     * Ends a previous started dialog.
     *
     * @return string
     * @throws AdapterException
     * @throws CurlException
     * @throws FailedRequestException
     */
    public function endDialog()
    {
        $endMsg = new Message(
            $this->bankCode,
            $this->username,
            $this->pin,
            $this->systemId,
            $this->dialogId,
            $this->messageNumber,
            array(
                new HKEND(3, $this->dialogId)
            )
        );

        $response = $this->sendMessage($endMsg);

        $this->dialogId = 0;
        $this->messageNumber = 1;

        return $response->rawResponse;
    }
}
