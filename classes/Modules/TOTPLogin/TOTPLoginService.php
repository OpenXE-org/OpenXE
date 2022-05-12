<?php

namespace Xentral\Modules\TOTPLogin;

use DateTime;
use Exception;
use Xentral\Components\Barcode\BarcodeFactory;
use Xentral\Components\Barcode\Qrcode;
use Xentral\Components\Database\Database;
use Xentral\Components\Token\TOTPTokenManager;
use Xentral\Modules\TOTPLogin\Exception\TOTPDisabledForUserException;
use Xentral\Modules\TOTPLogin\Exception\TOTPUserNonExistantException;

class TOTPLoginService
{
    /** @var Database $database */
    private $database;

    /** @var BarcodeFactory $barcodeService */
    private $barcodeService;

    /** @var TOTPTokenManager $tokenService */
    private $tokenService;

    /** @var string $userTable */
    private $userTable = 'user';

    /**
     * @param Database         $database
     * @param BarcodeFactory   $barcodeFactory
     * @param TOTPTokenManager $tokenService
     */
    public function __construct(Database $database, BarcodeFactory $barcodeFactory, TOTPTokenManager $tokenService)
    {
        $this->database = $database;
        $this->barcodeService = $barcodeFactory;
        $this->tokenService = $tokenService;
    }

    /**
     * @param int $userId
     *
     * @throws TOTPUserNonExistantException
     *
     * @return bool
     */
    public function isTOTPEnabled($userId)
    {
        if (!$this->existsUser($userId)) {
            throw TOTPUserNonExistantException::fromUserId($userId);
        }

        $query = $this->database->select()
            ->from('user_totp')
            ->cols(['active'])
            ->where('user_id = :user_id')
            ->bindValue('user_id', $userId);

        $result = $this->database->fetchValue($query->getStatement(), $query->getBindValues());

        return $result === 1;
    }

    /**
     * @param int    $userId
     * @param string $token
     *
     * @throws TOTPUserNonExistantException
     * @throws TOTPDisabledForUserException
     * @throws Exception
     *
     * @return bool
     */
    public function isTokenValid($userId, $token)
    {
        if (!$this->existsUser($userId)) {
            throw TOTPUserNonExistantException::fromUserId($userId);
        }

        if (!$this->isTOTPEnabled($userId)) {
            throw TOTPDisabledForUserException::fromUserId($userId);
        }

        $token = preg_replace('/[^0-9]/', '', $token);

        $now = (new DateTime())->getTimestamp();

        $userSecret = $this->getTOTPSecret($userId);

        return $this->tokenService->isTokenValid($token, $userSecret, $now);
    }

    /**
     * @param int $userId
     *
     * @return bool
     */
    private function existsUser($userId)
    {
        $selectQuery = $this->database->select()
            ->cols(['COUNT(*)'])
            ->from($this->userTable)
            ->where('id = :id')
            ->bindValue('id', $userId);

        $result = $this->database->fetchValue($selectQuery->getStatement(), $selectQuery->getBindValues());

        return $result !== 0;
    }

    /**
     * @param int $userId
     *
     * @throws TOTPUserNonExistantException
     *
     * @return void
     */
    public function enableTotp($userId)
    {
        if (!$this->existsUser($userId)) {
            throw TOTPUserNonExistantException::fromUserId($userId);
        }

        $fetchQuery = $this->database->select()
            ->from('user_totp')
            ->cols(['id'])
            ->where('user_id = :user_id')
            ->bindValue('user_id', $userId);

        $users = $this->database->fetchCol($fetchQuery->getStatement(), $fetchQuery->getBindValues());

        if (empty($users)) {
            $insertQuery = $this->database->insert()
                ->into('user_totp')
                ->cols(
                    [
                        'user_id' => $userId,
                        'active'  => 1,
                        'secret'  => $this->tokenService->generateBase32Secret(),
                    ]
                )
                ->set('created_at', 'NOW()');

            $this->database->perform($insertQuery->getStatement(), $insertQuery->getBindValues());
        } else {
            $updateQuery = $this->database->update()
                ->table('user_totp')
                ->cols(['active' => 1])
                ->where('user_id = :user_id')
                ->bindValue('user_id', $userId)
                ->set('modified_at', 'NOW()');

            $this->database->perform($updateQuery->getStatement(), $updateQuery->getBindValues());
        }
    }

    /**
     * @param int $userId
     *
     * @throws TOTPUserNonExistantException
     *
     * @return void
     */
    public function disableTotp($userId)
    {
        if (!$this->existsUser($userId)) {
            throw TOTPUserNonExistantException::fromUserId($userId);
        }

        $updateQuery = $this->database->update()
            ->table('user_totp')
            ->cols(['active' => 0])
            ->where('user_id = :user_id')
            ->bindValue('user_id', $userId)
            ->set('modified_at', 'NOW()');

        $this->database->perform($updateQuery->getStatement(), $updateQuery->getBindValues());
    }

    /**
     * @param int $userId
     *
     * @throws TOTPUserNonExistantException
     * @throws TOTPDisabledForUserException
     *
     * @return void
     */
    public function regenerateUserSecret($userId)
    {
        if (!$this->existsUser($userId)) {
            throw TOTPUserNonExistantException::fromUserId($userId);
        }

        if (!$this->isTOTPEnabled($userId)) {
            throw TOTPDisabledForUserException::fromUserId($userId);
        }

        $updateQuery = $this->database->update()
            ->table('user_totp')
            ->cols(
                [
                    'active' => 1,
                    'secret' => $this->tokenService->generateBase32Secret(),
                ]
            )
            ->set('modified_at', 'NOW()')
            ->where('user_id = :user_id')
            ->bindValue('user_id', $userId);

        $this->database->perform($updateQuery->getStatement(), $updateQuery->getBindValues());
    }

    /**
     * @param int $userId
     *
     * @throws TOTPUserNonExistantException
     * @throws TOTPDisabledForUserException
     *
     * @return string
     */
    public function getTOTPSecret($userId)
    {
        if (!$this->existsUser($userId)) {
            throw TOTPUserNonExistantException::fromUserId($userId);
        }

        if (!$this->isTOTPEnabled($userId)) {
            throw TOTPDisabledForUserException::fromUserId($userId);
        }

        $selectQuery = $this->database->select()
            ->from('user_totp')
            ->cols(['secret'])
            ->where('user_id = :user_id')
            ->bindValue('user_id', $userId);

        return (string)$this->database->fetchValue($selectQuery->getStatement(), $selectQuery->getBindValues());
    }

    public function setUserSecret($userId, $secret){

        if (!$this->existsUser($userId)) {
            throw TOTPUserNonExistantException::fromUserId($userId);
        }

        if (!$this->isTOTPEnabled($userId)) {
            throw TOTPDisabledForUserException::fromUserId($userId);
        }

        $updateQuery = $this->database->update()
            ->table('user_totp')
            ->cols(['secret' => $secret])
            ->where('user_id=:user')
            ->bindValue('user', $userId);

        $this->database->perform($updateQuery->getStatement(), $updateQuery->getBindValues());
    }

    /**
     * @param int    $userId
     * @param string $label
     *
     * @param null|string   $secret
     *
     * @return Qrcode
     */
    public function generatePairingQrCode($userId, $label, $secret = null)
    {
        if($secret == null){
            $secret = $this->getTOTPSecret($userId);
        }

        $query = "otpauth://totp/{$label}?secret=" . $secret . '&issuer=Xentral&algorithm=SHA1&digits=6&period=30';

        return $this->barcodeService->createQrCode($query);
    }
}
