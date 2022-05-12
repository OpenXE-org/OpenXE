<?php

namespace Xentral\Components\Http\Session;

use Xentral\Components\Http\Exception\SessionException;

class SessionHandler
{
    /**
     * Create a session object with actual session data
     *
     * @throws SessionException
     *
     * @return Session Session object
     */
    public static function createSession()
    {
        if (!extension_loaded('session')) {
            throw new SessionException('PHP extension "session" is missing.');
        }

        if (self::isSessionStarted()) {
            throw new SessionException('Failed to create session. Session can be started only once.');
        }
        if (session_status() === PHP_SESSION_DISABLED) {
            throw new SessionException('Failed to create session. Sessions are disabled.');
        }

        $isStarted = session_start();
        if ($isStarted === false) {
            throw new SessionException('Failed to create session. Initialization failed.');
        }

        if (session_status() === PHP_SESSION_NONE) {
            throw new SessionException('Failed to create session. Unexpected status: PHP_SESSION_NONE');
        }

        return new Session($_SESSION);
    }

    /**
     * @return bool
     */
    public static function isSessionStarted()
    {
        $status = session_status();

        return $status === PHP_SESSION_ACTIVE;
    }

    /**
     * Save and close the session
     *
     * @param Session $session
     *
     * @return void
     */
    public static function commitSession(Session $session)
    {
        $session->dumpSession($_SESSION);
        session_write_close();
    }
}
