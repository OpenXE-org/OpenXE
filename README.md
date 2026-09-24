# OpenXE - The free ERP

![OpenXE overview](https://github.com/openxe-org/OpenXE/blob/master/www/themes/new/images/login_screen_picture.jpg "OpenXE")

OpenXE ist eine umfassende webbasierte Anwendung zur Abwicklung aller kaufmännischen Prozesse. Zu den Funktionen gehören unter Anderem:

* Erstellung von Angeboten
* Auftragsabwicklung
* Rechnungsstellung
* Bestellung
* Lagerverwaltung
* Kundenkommunikation
* Aufgaben- und Terminverwaltung
* Zeitabrechnung

# Community-Seite: [https://openxe.org/](https://openxe.org/community/)

An alle Interessenten:

Dieses Projekt basiert auf einer leistungsfähigen Software, die auf freiwilliger Basis kostenfrei weiterentwickelt und gepflegt wird. Wir bitten daher alle Interessenten für OpenXE, sich in der Community anzumelden. Die Anmeldung ist kostenfrei und mit keinerlei Verpflichtungen versehen. Ausserdem habt Ihr den Vorteil, Euch bei Fragen direkt im Communitybereich zu melden oder selber aktiv mitzugestalten.

Wir freuen uns über Eure Teilnahme, egal ob als stiller Mitleser oder aktiver User.

# Releases
https://github.com/OpenXE-org/OpenXE/releases

# Installation

[Hier gehts zur Server Installation](SERVER_INSTALL.md)

[Hier gehts zur OpenXE Installation](INSTALL.md)

# API-Dokumentation

Die REST-API wird automatisch aus dem Code generiert.

## Dokumentation lokal generieren

```bash
# OpenAPI 3.0 (JSON)
php tools/generate-api-docs.php --format=openapi

# RAML
php tools/generate-api-docs.php --format=raml
```

Generierte Dateien:
- `www/api/openapi.json` - OpenAPI 3.0 Spezifikation
- `www/api/docs.generated.raml` - RAML Spezifikation

## Dokumentation ansehen

Nach der Installation ist die API-Dokumentation unter folgenden URLs verfügbar (mit API-Account Login):
- `/api/swagger.html` - Interaktive Swagger UI
- `/api/docs.html` - RAML HTML-Dokumentation
- `/api/openapi.json` - OpenAPI JSON (für Tools wie Postman)

Die Dokumentation wird automatisch durch GitHub Actions bei jedem Push generiert.

---

OpenXE ist freie Software, lizensiert unter der EGPL 3.1.
Diese Software ist eine Ableitung und Veränderung von Xentral ERP, Opensource Version. Xentral ERP wurde von embedded projects GmbH als Wawision und später Xentral entwickelt und steht unter der EGPLv3.1-Lizenz als Open Source Software. Informationen zu Xentral findet man unter http://www.xentral.de
