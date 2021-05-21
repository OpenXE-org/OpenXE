<?php

namespace Xentral\Modules\Api\Error;

class ApiError
{
    /*
     * Auth-Fehler
     */
    const CODE_UNAUTHORIZED = 7411; // (Erster) Besuch ohne Authorization-Header
    const CODE_DIGEST_HEADER_INCOMPLETE = 7412; // Digest-Header unvollständig; benötigte Teile fehlen
    const CODE_API_ACCOUNT_MISSING = 7413; // Es ist überhaupt kein API-Account angelegt oder aktiv
    const CODE_API_ACCOUNT_INVALID = 7414; // Verwendeter API-Account ist nicht (mehr?) gültig oder aktiv
    //const CODE_DIGEST_VALIDDATION_FAILED = 7415; // Prüfung ist fehlgeschlagen // Momentan nicht möglich da es mehrere Accounts mit dem gleichen Benutzernamen geben kann.
    const CODE_DIGEST_NONCE_INVALID = 7416; // Serverkey ist nicht vorhanden, oder schon länger abgelaufen (daher gelöscht)
    const CODE_DIGEST_NONCE_EXPIRED = 7417; // Serverkey ist abgelaufen
    const CODE_AUTH_USERNAME_EMPTY = 7418; // Benutzername wurde leer übergeben
    const CODE_AUTH_TYPE_NOT_ALLOWED = 7419; // Authorization-Header vorhanden, aber kein Digest
    const CODE_DIGEST_NC_NOT_MATCHING = 7420; // NonceCount (nc) passt nicht
    const CODE_API_ACCOUNT_PERMISSION_MISSING = 7421; // Api account has not the correct permissions

    /*
     * Routing-Fehler
     */
    const CODE_ROUTE_NOT_FOUND = 7431;
    const CODE_METHOD_NOT_ALLOWED = 7432;
    const CODE_API_METHOD_NOT_FOUND = 7433;

    /*
     * Endpoint-Fehler
     */
    const CODE_BAD_REQUEST = 7451; // API-Benutzer hat beim Request einen Fehler gemacht; Diesen Fehler nur verwenden
    // wenns nicht anders geht. Besser einen konkreteren Code verwenden bzw. anlegen. Benutzer kann mit diesem Fehler
    // nichts anfangen.

    const CODE_RESOURCE_NOT_FOUND = 7452; // API-Resource wurde nicht gefunden; zb wenn gesuchte ID nicht existiert
    const CODE_VALIDATION_ERROR = 7453; // Fehler bei der Validierung von Eingabedaten (nur bei PUT oder POST)
    const CODE_INVALID_ARGUMENT = 7454; // Argument (z.B. Suchparameter) enthält ungültige Werte
    const CODE_MALFORMED_REQUEST_BODY = 7455; // JSON oder XML konnte nicht dekodiert werden
    const CODE_CONTENT_TYPE_NOT_SUPPORTED = 7456; // Request-Body wurde mit unbekanntem Content-Type abgeschickt

    /*
     * Webserver falsch konfiguriert (Vermutlich Nginx oder FastCGI falsch konfiguriert)
     * @see https://www.nginx.com/resources/wiki/start/topics/examples/phpfcgi/
     */
    const CODE_WEBSERVER_MISCONFIGURED = 7481; // Fehlkonfiguration im Webserver (nicht genauer beschrieben). Diesen
    // Fehler-Code nicht verwenden! Besser einen konkreteren Fehlercode verwenden bzw. hinzufügen.

    const CODE_WEBSERVER_PATHINFO_INVALID = 7482; // $_SERVER['PATH_INFO'] ist nicht vorhanden oder leer, obwohl der
    // Request darauf hindeutet dass PATH_INFO gefüllt sein sollte.
    // Nginx bzw. FastCGI sehr wahrscheinlich falsch konfiguriert.

    /*
     * Sonstige Fehler
     */
    const CODE_UNEXPECTED_ERROR = 7499; // Schwerer Fehler; z.B. ungefangene Exception oder Fatal Error (unsere Schuld)
}
