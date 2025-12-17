<style>
.upgrade-status-card {background:#f6f8fb;border:1px solid #dbe3ef;border-radius:6px;padding:12px;margin-bottom:16px;}
.status-pill {display:inline-block;padding:4px 10px;border-radius:12px;font-weight:600;font-size:12px;margin-bottom:6px;}
.status-info {background:#e8f1ff;color:#0b3c68;}
.status-success {background:#e6f4ea;color:#1b6e30;}
.status-error {background:#fdecea;color:#b52b27;}
.status-warning {background:#fff4e5;color:#8a4b0f;}
.card-success {border-color:#1b6e30;background:#e9f7ef;}
.card-error {border-color:#b52b27;background:#fdeceb;}
.card-warning {border-color:#d89216;background:#fff7e9;}
.card-info {border-color:#0b3c68;background:#eef4ff;}
.status-meta {color:#555;margin-top:4px;font-size:13px;}
.log-box {background:#0f1720;color:#e5e7eb;border-radius:6px;padding:10px;max-height:420px;overflow:auto;font-family:Consolas,monospace;font-size:13px;}
.hint {color:#555;font-size:13px;}
.input-inline {width:100%;padding:6px;border:1px solid #ccc;border-radius:4px;}
.action-btn {width:100%;margin-bottom:6px;}
.result-banner {border-radius:6px;padding:12px;margin-bottom:12px;font-weight:700;}
.banner-success {background:#1b6e30;color:#fff;}
.banner-error {background:#b52b27;color:#fff;}
.banner-warning {background:#d89216;color:#fff;}
.banner-info {background:#0b3c68;color:#fff;}
.status-guidance {margin-top:8px;font-weight:600;}
.status-guidance small {font-weight:400;display:block;margin-top:2px;}
.status-bar {display:flex;justify-content:space-between;align-items:flex-start;gap:12px;}
.status-text {flex:1;min-width:0;}
.banner-actions {display:flex;flex-wrap:wrap;gap:6px;justify-content:flex-end;align-items:flex-start;max-width:45%;}
.banner-btn {background:#0b3c68;color:#fff;border:none;border-radius:4px;padding:8px 14px;font-weight:600;cursor:pointer;min-height:36px;}
.banner-btn:hover {opacity:0.9;}
.icon-btn {width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;padding:0;align-self:center;}
</style>

<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE]
        <form action="" method="post">
            [FORMHANDLEREVENT]
            <input type="hidden" name="details_anzeigen" value="1">
            <input type="hidden" name="db_details_anzeigen" value="1">
            <div class="row">
                <div class="row-height">
                    <div class="col-xs-14 col-md-10 col-md-height">
                        <div class="inside inside-full-height">
                            <div class="result-banner banner-[STATUS_LEVEL] status-bar">
                                <div class="status-text">
                                    <div style="font-size:16px;">[STATUS_HEADLINE]</div>
                                    <div style="font-weight:400;">[STATUS_MESSAGE]</div>
                                    <div class="status-guidance">[GUIDANCE_TITLE]<small>[GUIDANCE_MESSAGE]</small></div>
                                </div>
                                <div class="banner-actions">
                                    <button name="submit" value="[UPGRADE_BUTTON_ACTION]" class="banner-btn" title="Code & DB prüfen/aktualisieren">[UPGRADE_BUTTON_LABEL]</button>
                                    <div [UPGRADE_FORCE_VISIBLE] style="display:flex;align-items:center;gap:6px;font-size:12px;color:#f0f6ff;">
                                        <input type="checkbox" name="erzwingen" value="1" [ERZWINGEN]>
                                        <label style="margin:0;padding:0;">Erzwingen (-f)</label>
                                    </div>
                                    <button name="submit" value="[UPGRADE_DB_BUTTON_ACTION]" class="banner-btn" title="Datenbank prüfen/aktualisieren">[UPGRADE_DB_BUTTON_LABEL]</button>
                                    <div [UPGRADE_DB_FORCE_VISIBLE] style="display:flex;align-items:center;gap:6px;font-size:12px;color:#f0f6ff;">
                                        <input type="checkbox" name="erzwingen" value="1" [ERZWINGEN]>
                                        <label style="margin:0;padding:0;">Erzwingen (-f)</label>
                                    </div>
                                    <button name="submit" value="refresh" class="banner-btn icon-btn" title="Anzeige neu laden">&#x21bb;</button>
                                </div>
                            </div>
                            <fieldset class="upgrade-status-card card-[STATUS_LEVEL]">
                                <div class="status-pill status-[STATUS_LEVEL]">[STATUS_HEADLINE]</div>
                                <div><strong>Status:</strong> [STATUS_MESSAGE]</div>
                                <div class="status-guidance">[GUIDANCE_TITLE]<small>[GUIDANCE_MESSAGE]</small></div>
                                <div class="status-meta"><strong>Letzte Aktion:</strong> [LAST_ACTION]</div>
                                <div class="status-meta"><strong>Letzter Durchlauf:</strong> [LAST_RUN]</div>
                                <div class="status-meta"><strong>Installiert:</strong> OpenXE [CURRENT]</div>
                                <div class="status-meta"><strong>Lokaler Branch:</strong> [LOCAL_BRANCH] <span class="hint">[LOCAL_COMMIT]</span></div>
                                <div class="status-meta"><strong>Ziel (Upgrade-Quelle):</strong> [REMOTE_HOST] (<strong>[REMOTE_BRANCH]</strong>)</div>
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
                                <legend>{|Versionsvergleich|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td><strong>Installiert</strong></td><td>OpenXE [CURRENT]</td></tr>
                                    <tr><td><strong>Lokaler Branch</strong></td><td>[LOCAL_BRANCH] <span class="hint">[LOCAL_COMMIT]</span></td></tr>
                                    <tr><td><strong>Upgrade-Ziel</strong></td><td>[REMOTE_BRANCH] @ [REMOTE_HOST]</td></tr>
                                </table>
                            </fieldset>
                            <fieldset>
                                <legend>{|Protokoll|}</legend>
                                <div class="log-box">[OUTPUT_FROM_CLI]</div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="row-height">
                    <div class="col-xs-14 col-md-10 col-md-height">
                        <div class="inside inside-full-height">
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
