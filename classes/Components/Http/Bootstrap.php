<?php

namespace Xentral\Components\Http;

use Xentral\Components\Http\Session\SessionHandler;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'Request' => 'onInitRequest',
            'Session' => 'onInitSession',
        ];
    }

    /**
     * @return Request
     */
    public static function onInitRequest()
    {
        return Request::createFromGlobals();
    }

    /**
     * @return Session\Session
     */
    public static function onInitSession()
    {
        return SessionHandler::createSession();
    }
}
