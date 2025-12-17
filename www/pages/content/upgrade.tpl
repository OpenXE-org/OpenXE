<style>
.status-banner {border-radius:6px;padding:14px;margin-bottom:12px;color:#fff;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;height:100%;}
.banner-success {background:#1b6e30;}
.banner-error {background:#b52b27;}
.banner-warning {background:#d89216;}
.banner-info {background:#0b3c68;}
.banner-text {flex:1;min-width:0;}
.banner-headline {font-size:18px;font-weight:700;}
.banner-sub {font-size:14px;margin-top:4px;}
.banner-guidance {margin-top:8px;font-weight:700;}
.banner-guidance small {display:block;font-weight:400;}
.banner-hint {margin-top:8px;font-size:13px;opacity:0.9;}
.banner-actions {display:flex;flex-wrap:wrap;gap:8px;align-items:center;justify-content:flex-end;}
.banner-btn {background:rgba(255,255,255,0.12);color:#fff;border:1px solid rgba(255,255,255,0.2);border-radius:5px;padding:8px 14px;font-weight:700;cursor:pointer;min-height:40px;}
.banner-btn:hover {background:rgba(255,255,255,0.18);}
.icon-btn {width:42px;height:42px;border-radius:21px;display:flex;align-items:center;justify-content:center;font-size:20px;padding:0;}
.hidden-force {display:block;width:100%;margin-top:6px;}
.hidden-force label {display:flex;align-items:center;gap:6px;margin:0;font-size:12px;color:rgba(255,255,255,0.9);}
.top-row {display:grid;grid-template-columns: minmax(420px, 2fr) minmax(320px, 1fr);gap:16px;align-items:stretch;margin-bottom:32px;position:relative;z-index:1;box-sizing:border-box;}
.status-col {display:flex;}
.steps-col {display:flex;}
.steps-stack {display:flex;flex-direction:column;gap:12px;width:100%;}
.info-row {display:grid;grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));gap:16px;align-items:stretch;margin-bottom:24px;margin-top:16px;}
.compare-row {display:grid;grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));gap:16px;align-items:stretch;margin-bottom:16px;margin-top:8px;}
.log-section {margin-top:24px;clear:both;position:relative;z-index:0;}
.stepper {display:flex;flex-direction:column;gap:12px;margin-bottom:12px;}
.step-card {border:1px solid #dbe3ef;border-radius:6px;background:#f6f8fb;padding:12px;}
.step-head {display:flex;align-items:center;justify-content:space-between;gap:8px;}
.pill {display:inline-block;padding:4px 10px;border-radius:12px;font-size:12px;font-weight:700;}
.pill-success {background:#e6f4ea;color:#1b6e30;}
.pill-error {background:#fdecea;color:#b52b27;}
.pill-warning {background:#fff4e5;color:#8a4b0f;}
.pill-info {background:#e8f1ff;color:#0b3c68;}
.step-actions {display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;}
.step-btn {background:#0b3c68;color:#fff;border:none;border-radius:4px;padding:8px 12px;font-weight:700;cursor:pointer;}
.step-btn:hover {opacity:0.9;}
.force-wrap {margin-top:6px;font-size:12px;color:#0b3c68;}
.card {border:1px solid #dbe3ef;border-radius:6px;background:#fff;padding:12px;margin-bottom:12px;}
.log-box {background:#0f1720;color:#e5e7eb;border-radius:6px;padding:10px;max-height:420px;overflow:auto;font-family:Consolas,monospace;font-size:13px;}
.hint {color:#555;font-size:13px;}
.input-inline {width:100%;padding:6px;border:1px solid #ccc;border-radius:4px;}
.action-btn {width:100%;margin-top:6px;}
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

            <div class="top-row">
                <div class="status-col">
                    <div class="status-banner banner-[STATUS_LEVEL]" style="width:100%;">
                        <div class="banner-text">
                            <div class="banner-headline">[STATUS_HEADLINE]</div>
                            <div class="banner-sub">[STATUS_MESSAGE]</div>
                            <div class="banner-guidance">[GUIDANCE_TITLE]<small>[GUIDANCE_MESSAGE]</small></div>
                            <div class="banner-hint">Das Upgrade läuft in zwei Schritten: Dateien aktualisieren und Datenbank auffrischen. Für lange Läufe kannst du das Protokoll mit "Neu laden" aktualisieren. Bei hartnäckigen Fehlern hilft der Konsolen-Run: <code>./upgrade.sh -do</code> im Unterordner <code>upgrade</code>.</div>
                        </div>
                        <div class="banner-actions">
                            <button name="submit" value="refresh" class="banner-btn icon-btn" title="Anzeige neu laden">&#x21bb;</button>
                        </div>
                    </div>
                </div>
                <div class="steps-col">
                    <div class="steps-stack">
                        <div class="step-card">
                            <div class="step-head">
                                <div>
                                    <div class="pill pill-[STATUS_LEVEL]">Dateien</div>
                                    <div><strong>Code & Repo</strong></div>
                                </div>
                                <div class="step-actions">
                                    <button name="submit" value="[UPGRADE_BUTTON_ACTION]" class="step-btn" style="width:100%;">[UPGRADE_BUTTON_LABEL]</button>
                                </div>
                            </div>
                            <div class="force-wrap" [UPGRADE_FORCE_VISIBLE]><label><input type="checkbox" name="erzwingen" value="1" [ERZWINGEN]> Erzwingen (-f)</label></div>
                        </div>
                        <div class="step-card">
                            <div class="step-head">
                                <div>
                                    <div class="pill pill-[STATUS_LEVEL]">Datenbank</div>
                                    <div><strong>DB-Check & Upgrade</strong></div>
                                </div>
                                <div class="step-actions">
                                    <button name="submit" value="[UPGRADE_DB_BUTTON_ACTION]" class="step-btn" style="width:100%;">[UPGRADE_DB_BUTTON_LABEL]</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="compare-row">
                <div class="status-col" style="flex:1;min-width:320px;">
                    <div class="card" style="height:100%;">
                        <legend><strong>{|Versionsabgleich|}</strong></legend>
                        <div class="status-meta"><strong>Installiert:</strong> OpenXE [CURRENT]</div>
                        <div class="status-meta"><strong>Lokaler Branch:</strong> [LOCAL_BRANCH] <span class="hint">[LOCAL_COMMIT]</span></div>
                        <div class="status-meta"><strong>Upgrade-Quelle:</strong> [REMOTE_HOST] (<strong>[REMOTE_BRANCH]</strong>)</div>
                        <div class="status-meta"><strong>Status:</strong> <span class="pill [BRANCH_ALIGNMENT_CLASS]">[BRANCH_ALIGNMENT]</span></div>
                        <div class="status-meta" [SHOW_SYNC_REMOTE] style="margin-top:6px;">
                            <button name="submit" value="sync_remote_to_local" class="ui-button-icon action-btn" style="width:100%;">Quelle auf lokalen Branch setzen</button>
                        </div>
                        <div class="status-meta" style="margin-top:6px;">
                            <button name="submit" value="reset_remote_origin" class="ui-button-icon action-btn" style="width:100%;">Quelle auf Original zurücksetzen</button>
                        </div>
                    </div>
                </div>
                <div class="status-col" style="flex:1;min-width:320px;">
                    <div class="card" style="height:100%;">
                        <legend><strong>{|Upgrade-Quelle (Git)|}</strong></legend>
                        <table width="100%" border="0" class="mkTableFormular">
                            <tr><td colspan=2><div class="hint">Passe Remote-URL und Branch an, wenn du auf einen anderen Stand updaten willst.</div></td></tr>
                            <tr><td>Remote-URL:</td><td><input class="input-inline" type="text" name="remote_host" value="[REMOTE_HOST]" autocomplete="off"></td></tr>
                            <tr><td>Branch:</td><td><input class="input-inline" type="text" name="remote_branch" value="[REMOTE_BRANCH]" autocomplete="off"></td></tr>
                            <tr><td colspan=2><button name="submit" value="save_remote" class="ui-button-icon action-btn">Quelle speichern</button></td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="log-section">
                <div class="card">
                    <legend><strong>{|Protokoll|}</strong></legend>
                    <div class="log-box">[OUTPUT_FROM_CLI]</div>
                </div>
            </div>
        </form>
    </div>
</div>
