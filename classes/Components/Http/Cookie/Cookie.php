<?php

namespace Xentral\Components\Http\Cookie;

use DateTime;
use DateTimeInterface;
use Exception;
use Xentral\Components\Http\Exception\InvalidArgumentException;
use Xentral\Components\Util\StringUtil;

class Cookie
{
    /** @var string SAMESITE_LAX */
    const SAMESITE_LAX = 'Lax';

    /** @var string SAMESITE_STRICT */
    const SAMESITE_STRICT = 'Strict';

    /** @var string SAMESITE_NONE */
    const SAMESITE_NONE = '';

    /** @var string $name */
    private $name;

    /** @var string $value */
    private $value;

    /** @var DateTime $expire */
    private $expire;

    /** @var string $path */
    private $path;

    /** @var string $domain */
    private $domain;

    /** @var bool $secure */
    private $secure;

    /** @var bool $httpOnly */
    private $httpOnly;

    /** @var string $sameSite */
    private $sameSite;

    /**
     * @param string $name
     * @param string $value
     * @param int    $timeToLive
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httpOnly
     * @param string $sameSite
     */
    public function __construct(
        $name,
        $value,
        $timeToLive = 0,
        $path = '/',
        $domain = '',
        $secure = true,
        $httpOnly = true,
        $sameSite = self::SAMESITE_STRICT
    ) {
        if (!$this->isValidCookieName($name)) {
            throw new InvalidArgumentException('Invalid Cookie name.');
        }
        if (!$this->isValidCookieValue($value)) {
            throw new InvalidArgumentException('Invalid Cookie value.');
        }
        $this->name = $name;
        $this->value = $value;
        $this->setTimeToLive($timeToLive);
        $this->setPath($path);
        $this->setDomain($domain);
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
        $this->setSameSite($sameSite);
    }

    /**
     * Returns string representation of cookie to be sent in Http response
     *
     * @return string
     */
    public function toHttpHeader()
    {
        $header = sprintf('Set-Cookie: %s=%s', $this->name, $this->value);
        if ($this->expire !== null) {
            $header .= sprintf('; Expires=%s',
                gmdate(DateTimeInterface::RFC7231, $this->expire->getTimestamp()));
        }
        if ($this->path !== '') {
            $header .= sprintf('; Path=%s', $this->path);
        }
        if ($this->domain !== '') {
            $header .= sprintf('; Domain=%s', $this->domain);
        }
        if ($this->isSecure()) {
            $header .= '; Secure';
        }
        if ($this->isHttpOnly()) {
            $header .= '; HttpOnly';
        }
        if (in_array($this->sameSite, [self::SAMESITE_LAX, self::SAMESITE_STRICT], true)) {
            $header .= sprintf('; SameSite=%s', $this->sameSite);
        }

        return StringUtil::toAscii($header);
    }

    /**
     * Sets cookie expiry time to current time
     *
     * The client will delete this cookie.
     *
     * @return void
     */
    public function expireNow()
    {
        $this->setTimeToLive(-1);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return DateTime
     */
    public function getExpire()
    {
        return $this->expire;
    }

    /**
     * Sets date and time of expiration of the cookie
     *
     * @param DateTimeInterface $expirationDate
     *
     * @return void
     */
    public function setExpirationDate(DateTimeInterface $expirationDate)
    {
        try {
            $this->expire = new DateTime($expirationDate->format(DateTimeInterface::RFC7231));
        } catch (Exception $e) {
            $this->expire = null;
            throw new InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * Sets date and time of expiration based on specific time to live
     *
     * @param int $timeToLive in seconds
     *
     * @return void
     */
    public function setTimeToLive($timeToLive)
    {
        if ($timeToLive === 0) {
            $this->expire = null;
        } else {
            try {
                $this->expire = new DateTime();
                $time = time() + $timeToLive;
                $this->expire->setTimestamp($time);
            } catch (Exception $e) {
                $this->expire = null;
                throw new InvalidArgumentException($e->getMessage());
            }
        }
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        if ($path === '' || $this->isValidCookieValue($path)) {
            $this->path = $path;
        } else {
            throw new InvalidArgumentException('Invalid path value.');
        }

    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        if ($domain === '' || $this->isValidCookieValue($domain)) {
            $this->domain = $domain;
        } else {
            throw new InvalidArgumentException('Invalid domain value.');
        }
    }

    /**
     * @return bool
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * Sets the Http secure flag
     *
     * @param bool $secure true=cookie will only be sent over secure connection
     */
    public function setSecure($secure)
    {
        $this->secure = $secure;
    }

    /**
     * @return bool
     */
    public function isHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * Sets the HttpOnly flag
     *
     * @param bool $httpOnly true=cookie will only be sent in http responses
     */
    public function setHttpOnly($httpOnly)
    {
        $this->httpOnly = $httpOnly;
    }

    /**
     * @return string
     */
    public function getSameSite()
    {
        return $this->sameSite;
    }

    /**
     * Sets the sameSite token
     *
     *Values:
     *  'lax':      cookie can be sent cross-site for top-level navigation and GET, HEAD, OPTIONS and TRACE requests
     *  'strict':   cookie can never be sent cross-site
     *  '':         disable the sameSite token
     *
     * @param string $sameSite values: 'lax'|'scrict'|'none'
     *
     * @return void
     */
    public function setSameSite($sameSite)
    {
        if (!in_array($sameSite, [self::SAMESITE_LAX, self::SAMESITE_STRICT, self::SAMESITE_NONE], true)) {
            throw new InvalidArgumentException('Invalid "samesite" attribute.');
        }
        $this->sameSite = $sameSite;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toHttpHeader();
    }

    /**
     * @param $name
     *
     * @return bool
     */
    protected function isValidCookieName($name)
    {
        return (bool)preg_match('/^[a-zA-Z0-9\\\\!#$%&\'*+.\-^_`|~]+$/', $name);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    protected function isValidCookieValue($value)
    {
        return (bool)preg_match('/^"?[a-zA-Z0-9\\\\!#$%&\'()*+\-.\/:<=>?@\[\]^_`{|}~]+"?$/', $value);
    }
}
