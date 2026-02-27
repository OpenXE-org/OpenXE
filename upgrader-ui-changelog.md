# Upgrader UI Changelog

## 2025-12-15
- Branch `upgrader-ui` neu von `master` erstellt, bestehende `local_test_branch` unverändert gelassen.
- Upgrade-UI umgebaut: Statuskarte (Deutsch) mit letzter Aktion/Zeit/Version, klarerer Log-Viewer, Aktionen zusammengefasst.
- Backend erweitert: Remote-URL und Branch können über die Oberfläche gesetzt werden (Validierung, Schreibvorgang nach `upgrade/data/remote.json`).
- Ergebnisanzeige pro Lauf (erfolgreich/Fehler/alles aktuell), Log-Fallback wenn noch kein Protokoll existiert.
- Checkboxen für Details und Erzwingen bleiben nach Requests erhalten.
- Nächster Schritt: Änderungen nach `local_test_branch` kopieren (Cherry-Pick geplant).
- Upgrade-Abläufe laufen im selben Tab (keine neuen Fenster).
- Versionsvergleich hinzugefügt (Installiert, lokaler Branch/Commit, Upgrade-Ziel).
- Status-Banner und farbige Karten je Ergebniszustand ergänzen; geführte Hinweise mit nächstem Schritt abhängig vom Lauf (z.B. „Upgrade empfohlen“ bei Differenzen, „Alles aktuell“ bei 0 Differenzen).
- Guided-Hinweise aus eigenem Feld entfernt; Hinweise erscheinen direkt im farbigen Statusbereich (Banner + Karte).
