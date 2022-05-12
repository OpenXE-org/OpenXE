<?php

namespace Xentral\Modules\Api\Auth;

use Xentral\Components\Database\Database;
use Xentral\Components\Http\Request;
use Xentral\Modules\Api\Error\ApiError;
use Xentral\Modules\Api\Exception\AuthorizationErrorException;

class DigestAuth
{
    /** @var Database $db */
    protected $db;

    /** @var Request $request */
    protected $request;

    /** @var bool $isAuthenticated Authentifizierung erfolgreich? */
    protected $isAuthenticated = false;

    /** @var bool $checkNonceCount Soll der NonceCount geprüft werden? */
    protected $checkNonceCount = false;

    /** @var int $nonceMaxAge Maximales Alter in Sekunden (86400 = 24 Stunden) */
    protected $nonceMaxAge = 86400;

    /** @var string $realm */
    protected $realm = 'Xentral-API';

    /** @var string $nonce Server-Nonce */
    protected $nonce;

    /** @var string $opaque */
    protected $opaque;

    /** @var array $digestParts Header-Bestandteile für Digest-Authentifizierung */
    protected $digestParts;

    /** @var int|null $apiAccountId */
    protected $apiAccountId;

    /**
     * @param Database $db
     * @param Request $request
     */
    public function __construct($db, $request)
    {
        $this->db = $db;
        $this->request = $request;

        // 30 Tage alte Serverkey löschen
        if (mt_rand(0, 99) === 0) {
            $this->db->exec('DELETE FROM `api_keys` WHERE zeitstempel < DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)');
        }
    }

    /**
     * @return void
     */
    public function checkLogin()
    {
        $authHeader = $this->getAuthorizationRequestHeader();
        if (!$authHeader) {
            throw new AuthorizationErrorException(
                'Unauthorized. You need to login.',
                ApiError::CODE_UNAUTHORIZED
            );
        }

        if (stripos($authHeader, 'digest ') !== 0) {
            throw new AuthorizationErrorException(
                'Authorization type not allowed.',
                ApiError::CODE_AUTH_TYPE_NOT_ALLOWED
            );
        }

        $digestHeader = $this->getDigestRequestHeader();
        if (!$digestHeader) {
            throw new AuthorizationErrorException(
                'Unauthorized. You need to login.',
                ApiError::CODE_UNAUTHORIZED
            );
        }

        // Parameter für Authentifizierung extrahieren
        $this->digestParts = $this->parseDigest($digestHeader);
        // Benötigte Teile im Digest-Header fehlen
        if ($this->digestParts === false) {
            throw new AuthorizationErrorException(
                'Authorization failure',
                ApiError::CODE_DIGEST_HEADER_INCOMPLETE
            );
        }

        // Benutzername wurde leer eingegeben
        if (empty($this->digestParts['username'])) {
            throw new AuthorizationErrorException(
                'Authorization failure. Username is empty.',
                ApiError::CODE_AUTH_USERNAME_EMPTY
            );
        }

        // Alle aktiven API-Zugänge aus DB laden
        $apiAccounts = $this->db->fetchAll(
            'SELECT a.remotedomain as appname, a.initkey, a.id FROM api_account AS a WHERE a.aktiv = 1'
        );

        if (empty($apiAccounts)) {
            throw new AuthorizationErrorException(
                'Authorization failure. API Account not existing.',
                ApiError::CODE_API_ACCOUNT_MISSING
            );
        }

        foreach ($apiAccounts as $account) {
            $validUser = $account['appname'];
            $validPass = $account['initkey'];

            // Username im Header stimmt nicht mit Account überein
            if ($validUser !== $this->digestParts['username']) {
                continue; // Nächsten Account probieren
            }

            // Digest-Algo validieren
            if (!$this->validateDigestLogin($validUser, $validPass)) {
                continue; // Mit nächsten Account weitermachen

                // @todo API-Accounts mit gleichen Usernamen verhindern?
                //throw new AuthorizationErrorException(
                //'Validation failure. Digest not valid.',
                // ApiError::CODE_DIGEST_VALIDDATION_FAILED
                //);
            }

            // Key-Details aus DB laden
            $keyDetails = $this->getKeyDetails($this->digestParts['nonce'], $this->digestParts['opaque']);

            // Authentifizierung war gültig; Serverkeys sind aber abgelaufen, oder Client hat sich die Keys ausgedacht
            if (!$keyDetails) {
                $this->nonce = $this->opaque = null;
                throw new AuthorizationErrorException(
                    'Authorization failure. Nonce is invalid or expired.',
                    ApiError::CODE_DIGEST_NONCE_INVALID
                );
            }

            // Serverkeys sind abgelaufen (aber noch vorhanden in DB)
            if ($keyDetails['age'] > $this->nonceMaxAge) {
                $this->nonce = $this->opaque = null;
                throw new AuthorizationErrorException(
                    'Authorization failure. Nonce is expired.',
                    ApiError::CODE_DIGEST_NONCE_EXPIRED
                );
            }

            // NonceCount prüfen?
            if ($this->checkNonceCount) {
                // NonceCount zu Hexadezimal wandeln
                $nonceCountHex = dechex($keyDetails['nonce_count_decimal']);
                $this->digestParts['nc'] = ltrim($this->digestParts['nc'], '0');

                // NonceCount stimmt nicht überein
                if ($this->digestParts['nc'] !== $nonceCountHex) {
                    throw new AuthorizationErrorException(
                        'Authorization failure. Nonce count doesn\'t match.',
                        ApiError::CODE_DIGEST_NC_NOT_MATCHING
                    );
                }
            }

            // NonceCount in DB hochzählen
            $this->incrementNonceCount($this->digestParts['nonce']);

            // Wenn bis hierhin kein Fehler passiert ist, passt alles.
            // Serverkeys sind noch gültig
            $this->isAuthenticated = true;
            $this->apiAccountId = (int)$account['id'];
            return;
        }

        // Alle Accounts durchprobiert > Kein Erfolg
        throw new AuthorizationErrorException(
            'Authorization failure. API Account invalid.',
            ApiError::CODE_API_ACCOUNT_INVALID
        );
    }

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        return $this->isAuthenticated;
    }

    /**
     * @return int|null
     */
    public function getApiAccountId()
    {
        return $this->apiAccountId;
    }

    /**
     * Header-String generieren den der Client zum Authentifizieren benötigt
     *
     * @return string
     */
    public function generateAuthenticationString()
    {
        // Neue Server-Key generieren
        if (!$this->nonce && !$this->opaque) {
            $this->createServerKeys();
        }

        return sprintf(
            'Digest realm="%s",qop="auth",nonce="%s",opaque="%s"',
            $this->realm, $this->nonce, $this->opaque
        );
    }

    /**
     * @param string $nonce
     * @param string $opaque
     *
     * @return array|bool
     */
    protected function getKeyDetails($nonce, $opaque)
    {
        if (empty($nonce) || empty($opaque)) {
            return false;
        }

        $keyDetails = $this->db->fetchAll(
            'SELECT k.nonce_count, k.zeitstempel FROM api_keys AS k '.
            'WHERE k.nonce = :nonce AND k.opaque = :opaque',
            array('nonce' => $nonce, 'opaque' => $opaque)
        );

        if (count($keyDetails) === 0) {
            return false;
        }

        return array(
            'nonce_count_decimal' => (int)$keyDetails[0]['nonce_count'],
            'age' => time() - strtotime($keyDetails[0]['zeitstempel']),
        );
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return bool Digest-Auth valide?
     */
    protected function validateDigestLogin($username, $password)
    {
        // Based on all the info we gathered we can figure out what the response should be
        $A1 = md5("{$username}:{$this->realm}:{$password}");
        $A2 = md5("{$this->request->getMethod()}:".stripslashes($this->request->getRequestUri()));

        // Im 'auth-int' Modus muss zusätzlich der Request-Body validiert werden
        if ($this->digestParts['qop'] === 'auth-int') {
            $A2 = md5("{$this->request->getMethod()}:".stripslashes($this->request->getRequestUri()).":{$this->request->getContent()}");
        }

        $validResponse = md5("{$A1}:{$this->digestParts['nonce']}:{$this->digestParts['nc']}:{$this->digestParts['cnonce']}:{$this->digestParts['qop']}:{$A2}");

        return ($this->digestParts['response'] === $validResponse);
    }

    /**
     * @param string $nonce
     */
    protected function incrementNonceCount($nonce)
    {
        $this->db->perform(
            'UPDATE api_keys SET nonce_count = nonce_count + 1 WHERE nonce = :nonce',
            array('nonce' => $nonce)
        );
    }

    /**
     * Neue Server-Keys (Nonce und Opaque) generieren und in DB ablegen
     */
    protected function createServerKeys()
    {
        $this->nonce = md5(uniqid('', true));
        $this->opaque = md5(uniqid('', true));

        // Neue Keys in Datenbank speichern
        $this->db->perform(
            'INSERT INTO api_keys (id, nonce, opaque) VALUES (NULL, :nonce, :opaque)',
            array('nonce' => $this->nonce, 'opaque' => $this->opaque)
        );
    }

    /**
     * This function returns the digest header
     *
     * @return string|false
     */
    protected function getDigestRequestHeader()
    {
        $authHeader = $this->getAuthorizationRequestHeader();
        if (stripos($authHeader, 'digest ') === 0) {
            return substr_replace($authHeader, '', 0, 7);
        }

        return false;
    }

    /**
     * Einzelnen Request-Header auslesen
     *
     * @param string $type z.B. "Authorization" oder "Content-Type"
     *
     * @return string|false
     */
    protected function getRequestHeader($type)
    {
        if ($this->request->header->has($type)) {
            return $this->request->header->get($type);
        }

        return false;
    }

    /**
     * @return string|false
     */
    protected function getAuthorizationRequestHeader()
    {
        return $this->getRequestHeader('Authorization');
    }

    /**
     * Digest-Header in einzelne Bestandteile zerlegen, und prüfen ob alle benötigten Teile vorhanden sind.
     *
     * @param string $digest
     *
     * @return array|false Einzelne Bestandteile als Array, oder false wenn Teile fehlen
     */
    protected function parseDigest($digest)
    {
        $neededParts = array(
            'nonce' => false,
            'opaque' => false,
            'nc' => false,
            'cnonce' => false,
            'qop' => false,
            'username' => false,
            'uri' => false,
            'response' => false,
        );
        $data = array();

        // Beispiel: username="Test", realm="API", nonce="5b308bec108f0", uri="/api/addresses", qop=auth, nc=00000029, ...
        $parts = explode(',', $digest);
        foreach ($parts as $part) {
            $atoms = explode('=', $part, 2);
            if (count($atoms) !== 2) {
                continue;
            }

            $key = trim($atoms[0], ' ');
            $val = trim($atoms[1], '"');
            $data[$key] = $val;
            unset($neededParts[$key]);
        }

        return empty($neededParts) ? $data : false;
    }
}
