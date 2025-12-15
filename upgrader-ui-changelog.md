# Upgrader UI Changelog

## 2025-12-15
- Branch `upgrader-ui` neu von `master` erstellt, bestehende `local_test_branch` unverändert gelassen.
- Upgrade-UI umgebaut: Statuskarte (Deutsch) mit letzter Aktion/Zeit/Version, klarerer Log-Viewer, Aktionen zusammengefasst.
- Backend erweitert: Remote-URL und Branch können über die Oberfläche gesetzt werden (Validierung, Schreibvorgang nach `upgrade/data/remote.json`).
- Ergebnisanzeige pro Lauf (erfolgreich/Fehler/alles aktuell), Log-Fallback wenn noch kein Protokoll existiert.
- Checkboxen für Details und Erzwingen bleiben nach Requests erhalten.
- Nächster Schritt: Änderungen nach `local_test_branch` kopieren (Cherry-Pick geplant).
