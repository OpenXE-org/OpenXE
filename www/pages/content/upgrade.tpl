<style>
.upgrade-status-card {background:#f6f8fb;border:1px solid #dbe3ef;border-radius:6px;padding:12px;margin-bottom:16px;}
.status-pill {display:inline-block;padding:4px 10px;border-radius:12px;font-weight:600;font-size:12px;margin-bottom:6px;}
.status-info {background:#e8f1ff;color:#0b3c68;}
.status-success {background:#e6f4ea;color:#1b6e30;}
.status-error {background:#fdecea;color:#b52b27;}
.status-warning {background:#fff4e5;color:#8a4b0f;}
.status-meta {color:#555;margin-top:4px;font-size:13px;}
.log-box {background:#0f1720;color:#e5e7eb;border-radius:6px;padding:10px;max-height:420px;overflow:auto;font-family:Consolas,monospace;font-size:13px;}
.hint {color:#555;font-size:13px;}
.input-inline {width:100%;padding:6px;border:1px solid #ccc;border-radius:4px;}
.action-btn {width:100%;margin-bottom:6px;}
</style>

<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE]
        <form action="" method="post">
            [FORMHANDLEREVENT]
            <div class="row">
                <div class="row-height">
                    <div class="col-xs-14 col-md-10 col-md-height">
                        <div class="inside inside-full-height">
                            <fieldset class="upgrade-status-card">
                                <div class="status-pill status-[STATUS_LEVEL]">[STATUS_HEADLINE]</div>
                                <div><strong>Status:</strong> [STATUS_MESSAGE]</div>
                                <div class="status-meta"><strong>Letzte Aktion:</strong> [LAST_ACTION]</div>
                                <div class="status-meta"><strong>Letzter Durchlauf:</strong> [LAST_RUN]</div>
                                <div class="status-meta"><strong>Aktuelle Version:</strong> OpenXE [CURRENT]</div>
                                <div class="status-meta"><strong>Upgrade-Quelle:</strong> [REMOTE_HOST] ([REMOTE_BRANCH])</div>
                            </fieldset>
                            <fieldset>
                                <legend>{|Hinweise zum Upgrade|}</legend>
                                <div class="hint">
                                    Das Upgrade läuft in zwei Schritten: Dateien aktualisieren und Datenbank auffrischen.
                                    Für lange Läufe kannst du das Protokoll mit "Anzeige auffrischen" neu laden. Bei hartnäckigen Fehlern
                                    hilft der Konsolen-Run: <code>./upgrade.sh -do</code> im Unterordner <code>upgrade</code>.
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>{|Protokoll|}</legend>
                                <div class="log-box">[OUTPUT_FROM_CLI]</div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="col-xs-14 col-md-4 col-md-height">
                        <div class="inside inside-full-height">
                            <fieldset>
                                <legend>{|Aktionen|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td colspan=2><button name="submit" value="refresh" class="ui-button-icon action-btn">Anzeige auffrischen</button></td></tr>
                                    <tr><td colspan=2><button name="submit" value="check_upgrade" class="ui-button-icon action-btn">Upgrades prüfen</button></td></tr>
                                    <tr><td>{|Upgrade-Details anzeigen|}:</td><td><input type="checkbox" name="details_anzeigen" value=1 [DETAILS_ANZEIGEN] size="20"></td></tr>
                                    <tr [UPGRADE_VISIBLE]><td colspan=2><button name="submit" formtarget="_blank" value="do_upgrade" class="ui-button-icon action-btn">Upgrade jetzt starten</button></td></tr>
                                    <tr [UPGRADE_VISIBLE]><td>{|Erzwingen (-f)|}:</td><td><input type="checkbox" name="erzwingen" value=1 [ERZWINGEN] size="20"></td></tr>
                                    <tr><td colspan=2><button name="submit" value="check_db" class="ui-button-icon action-btn">Datenbank prüfen</button></td></tr>
                                    <tr><td>{|Datenbank-Details anzeigen|}:</td><td><input type="checkbox" name="db_details_anzeigen" value=1 [DB_DETAILS_ANZEIGEN] size="20"></td></tr>
                                    <tr [UPGRADE_DB_VISIBLE]><td colspan=2><button name="submit" formtarget="_blank" value="do_db_upgrade" class="ui-button-icon action-btn">Datenbank-Upgrade</button></td></tr>
                                </table>
                            </fieldset>
                            <fieldset>
                                <legend>{|Upgrade-Quelle (Git)|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td colspan=2><div class="hint">Passe Remote-URL und Branch an, wenn du auf einen anderen Stand updaten willst.</div></td></tr>
                                    <tr><td>Remote-URL:</td><td><input class="input-inline" type="text" name="remote_host" value="[REMOTE_HOST]" autocomplete="off"></td></tr>
                                    <tr><td>Branch:</td><td><input class="input-inline" type="text" name="remote_branch" value="[REMOTE_BRANCH]" autocomplete="off"></td></tr>
                                    <tr><td colspan=2><button name="submit" value="save_remote" class="ui-button-icon action-btn">Quelle speichern</button></td></tr>
                                </table>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
