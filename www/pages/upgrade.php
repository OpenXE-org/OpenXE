<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class upgrade {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "upgrade_overview");        
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }  
 
    function upgrade_overview() {  
    
        $submit = $this->app->Secure->GetPOST('submit');
        $details_post = $this->app->Secure->GetPOST('details_anzeigen');
        $db_details_post = $this->app->Secure->GetPOST('db_details_anzeigen');
        $verbose = $details_post === null ? true : $details_post === '1';
        $db_verbose = $db_details_post === null ? true : $db_details_post === '1';
        $force = $this->app->Secure->GetPOST('erzwingen') === '1';
        $remote_host_input = trim((string)$this->app->Secure->GetPOST('remote_host'));
        $remote_branch_input = trim((string)$this->app->Secure->GetPOST('remote_branch'));

      	$this->app->Tpl->Set('DETAILS_ANZEIGEN', $verbose?"checked":"");
      	$this->app->Tpl->Set('DB_DETAILS_ANZEIGEN', $db_verbose?"checked":"");
        $this->app->Tpl->Set('ERZWINGEN', $force?"checked":"");

        include("../upgrade/data/upgrade.php");

        $logfile = "../upgrade/data/upgrade.log";
        $remote_config_file = "../upgrade/data/remote.json";
        upgrade_set_out_file_name($logfile);

        $this->app->Tpl->Set('UPGRADE_VISIBLE', "hidden");
        $this->app->Tpl->Set('UPGRADE_DB_VISIBLE', "hidden");
        $upgrade_available = false;
        $upgrade_db_available = false;

        $status_headline = "Bereit";
        $status_level = "info";
        $status_message = "Wähle eine Aktion, um den Upgrader zu starten.";
        $guidance_title = "Nächste Schritte";
        $guidance_message = "Aktion auswählen und starten.";
        $last_action = "Noch keine Aktion ausgeführt";
        $last_run = "";

        $remote_host = "";
        $remote_branch = "";
        $remote_errors = array();
        $original_remote_host = "";
        $original_remote_branch = "";

        $git_root = __DIR__;
        for ($i = 0; $i < 6; $i++) {
            if (is_dir($git_root."/.git")) {
                break;
            }
            $parent = dirname($git_root);
            if ($parent === $git_root) {
                break;
            }
            $git_root = $parent;
        }
        if (!is_dir($git_root."/.git")) {
            $git_root = "";
        }

        $git_branch = "";
        $git_commit = "";
        $local_hash = "";
        $local_hash_short = "";
        if ($git_root !== "") {
            $git_branch = trim((string)@shell_exec('git -C '.escapeshellarg($git_root).' rev-parse --abbrev-ref HEAD'));
            $git_commit = trim((string)@shell_exec('git -C '.escapeshellarg($git_root).' log -1 --date=short --pretty="%cd"'));
            $local_hash = trim((string)@shell_exec('git -C '.escapeshellarg($git_root).' rev-parse HEAD'));
            $local_hash_short = trim((string)@shell_exec('git -C '.escapeshellarg($git_root).' rev-parse --short=8 HEAD'));
        }

        $update_status_text = "Remote-Stand nicht geprüft.";
        $update_status_class = "pill-info";
        $remote_hash = "";
        $remote_hash_short = "";

        if (is_readable($remote_config_file)) {
            $remote_data_raw = file_get_contents($remote_config_file);
            $remote_data = json_decode($remote_data_raw, true) ?: array();
            $remote_host = $remote_data['host'] ?? "";
            $remote_branch = $remote_data['branch'] ?? "";
            $original_remote_host = $remote_data['original_host'] ?? "";
            $original_remote_branch = $remote_data['original_branch'] ?? "";
        } else {
            $status_headline = "Hinweis";
            $status_level = "warning";
            $status_message = "Konfiguration der Upgrade-Quelle konnte nicht geladen werden.";
        }

        if ($submit === 'save_remote') {
            if ($remote_host_input === '') {
                $remote_errors[] = "Git-Remote darf nicht leer sein.";
            }
            if ($remote_branch_input === '') {
                $remote_errors[] = "Branch darf nicht leer sein.";
            }
            $allowed_host_pattern = '/^[\\w@.:\\/-]+$/';
            if ($remote_host_input !== '' && !preg_match($allowed_host_pattern, $remote_host_input)) {
                $remote_errors[] = "Git-Remote enthält ungültige Zeichen.";
            }
            $allowed_branch_pattern = '/^[A-Za-z0-9._\\/-]+$/';
            if ($remote_branch_input !== '' && !preg_match($allowed_branch_pattern, $remote_branch_input)) {
                $remote_errors[] = "Branch enthält ungültige Zeichen.";
            }

            if (empty($remote_errors)) {
                $payload = json_encode(
                    array(
                        'host' => $remote_host_input,
                        'branch' => $remote_branch_input,
                        'original_host' => $remote_host_input,
                        'original_branch' => $remote_branch_input
                    ),
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                );
                if (file_put_contents($remote_config_file, $payload) === false) {
                    $remote_errors[] = "Upgrade-Quelle konnte nicht gespeichert werden.";
                } else {
                    $remote_host = $remote_host_input;
                    $remote_branch = $remote_branch_input;
                    $original_remote_host = $remote_host_input;
                    $original_remote_branch = $remote_branch_input;
                    $status_headline = "Upgrade-Quelle gespeichert";
                    $status_level = "success";
                    $status_message = "Remote und Branch wurden übernommen.";
                }
            } else {
                $status_headline = "Eingabefehler";
                    $status_level = "error";
                    $status_message = implode(" ", $remote_errors);
            }
        } elseif ($submit === 'reset_remote_origin') {
            if ($original_remote_host === "" || $original_remote_branch === "") {
                $remote_errors[] = "Kein Original-Remote hinterlegt.";
            }
            if (empty($remote_errors)) {
                $payload = json_encode(
                    array(
                        'host' => $original_remote_host,
                        'branch' => $original_remote_branch,
                        'original_host' => $original_remote_host,
                        'original_branch' => $original_remote_branch
                    ),
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                );
                if (file_put_contents($remote_config_file, $payload) === false) {
                    $remote_errors[] = "Upgrade-Quelle konnte nicht zurückgesetzt werden.";
                } else {
                    $remote_host = $original_remote_host;
                    $remote_branch = $original_remote_branch;
                    $status_headline = "Upgrade-Quelle zurückgesetzt";
                    $status_level = "info";
                    $status_message = "Remote/Branch auf Originalwerte gestellt.";
                }
            } else {
                $status_headline = "Eingabefehler";
                $status_level = "error";
                $status_message = implode(" ", $remote_errors);
            }
        }

        // Calculate version alignment (local vs. upgrade source)
        if ($git_root !== "" && $remote_host !== "" && $remote_branch !== "") {
            $remote_ref = "refs/heads/".$remote_branch;
            $remote_line = trim((string)@shell_exec(
                'git -C '.escapeshellarg($git_root).' ls-remote '.escapeshellarg($remote_host).' '.escapeshellarg($remote_ref)
            ));
            if ($remote_line !== "") {
                $remote_hash = trim(strtok($remote_line, "\t "));
                $remote_hash_short = substr($remote_hash, 0, 8);
                if ($local_hash !== "" && $local_hash === $remote_hash) {
                    $update_status_text = "Alles aktuell";
                    $update_status_class = "pill-success";
                } elseif ($local_hash !== "" && $local_hash !== $remote_hash) {
                    $update_status_text = "Update verfügbar";
                    $update_status_class = "pill-warning";
                } else {
                    $update_status_text = "Lokaler Stand unbekannt";
                    $update_status_class = "pill-warning";
                }
            } else {
                $update_status_text = "Remote nicht erreichbar";
                $update_status_class = "pill-warning";
            }
        }

        $this->app->Tpl->Set('REMOTE_HOST', htmlspecialchars($remote_host));
        $this->app->Tpl->Set('REMOTE_BRANCH', htmlspecialchars($remote_branch));
        $this->app->Tpl->Set('REMOTE_ORIGINAL_HOST', htmlspecialchars($original_remote_host));
        $this->app->Tpl->Set('REMOTE_ORIGINAL_BRANCH', htmlspecialchars($original_remote_branch));
        $this->app->Tpl->Set('LOCAL_BRANCH', htmlspecialchars($git_branch));
        $this->app->Tpl->Set('LOCAL_COMMIT', htmlspecialchars($git_commit));
        $this->app->Tpl->Set('LOCAL_HASH_SHORT', htmlspecialchars($local_hash_short));
        $this->app->Tpl->Set('REMOTE_HASH_SHORT', htmlspecialchars($remote_hash_short));
        $this->app->Tpl->Set('UPDATE_STATUS', htmlspecialchars($update_status_text));
        $this->app->Tpl->Set('UPDATE_STATUS_CLASS', $update_status_class);
        $this->app->Tpl->Set('SHOW_SYNC_REMOTE', "hidden");
        $show_local_branch = ($git_branch !== "" && $remote_branch !== "" && $git_branch === $remote_branch);
        $this->app->Tpl->Set('LOCAL_BRANCH_VISIBLE', $show_local_branch ? "" : "hidden");

        $directory = dirname(getcwd())."/upgrade";
        $result_code = null;

        switch ($submit) {
            case 'check_upgrade':
                $last_action = "System-Check (Dateien & Datenbank)";
                $this->app->Tpl->Set('UPGRADE_VISIBLE', "");
                $upgrade_available = true;
                if (file_exists($logfile)) {
                    unlink($logfile);
                }
                $result_code = upgrade_main(   directory: $directory,
                                               verbose: $verbose,
                                               check_git: true,
                                               do_git: false,
                                               export_db: false,
                                               check_db: true,
                                               strict_db: false,
                                               do_db: false,
                                               force: $force,
                                               connection: false,
                                               origin: false,
                                               drop_keys: false
                );
            break;
            case 'do_upgrade':
                $last_action = "Upgrade (Dateien & Datenbank)";
                if (file_exists($logfile)) {
                    unlink($logfile);
                }
                
                // Erstelle Rollback-Tag vor Upgrade
                if ($git_root !== "") {
                    $tag_name = 'pre-upgrade-'.date('Y-m-d-H-i-s');
                    $tag_cmd = 'git -C '.escapeshellarg($git_root).' tag '.escapeshellarg($tag_name).' 2>&1';
                    $tag_output = shell_exec($tag_cmd);
                    if ($tag_output === null || trim($tag_output) === "") {
                        // Tag erfolgreich erstellt
                        $_SESSION['last_rollback_tag'] = $tag_name;
                    } else {
                        // Tag-Erstellung fehlgeschlagen - loggen aber fortfahren
                        $this->app->erp->LogFile(
                            'Rollback tag creation failed',
                            ['tag_name' => $tag_name, 'output' => $tag_output],
                            'upgrade',
                            'rollback_tag_error'
                        );
                    }
                }
                
                $result_code = upgrade_main(   directory: $directory,
                                               verbose: $verbose,
                                               check_git: true,
                                               do_git: true,
                                               export_db: false,
                                               check_db: true,
                                               strict_db: false,
                                               do_db: true,
                                               force: $force,
                                               connection: false,
                                               origin: false,
                                               drop_keys: false
                );
            break;    
            case 'check_db':
                $last_action = "Datenbank-Check";
                $this->app->Tpl->Set('UPGRADE_DB_VISIBLE', "");
                $upgrade_db_available = true;
                if (file_exists($logfile)) {
                    unlink($logfile);
                }
                $result_code = upgrade_main(   directory: $directory,
                                               verbose: $db_verbose,
                                               check_git: false,
                                               do_git: false,
                                               export_db: false,
                                               check_db: true,
                                               strict_db: false,
                                               do_db: false,
                                               force: $force,
                                               connection: false,
                                               origin: false,
                                               drop_keys: false
                );
            break;    
            case 'do_db_upgrade':
                $last_action = "Datenbank-Upgrade";
                $this->app->Tpl->Set('UPGRADE_DB_VISIBLE', "");
                $upgrade_db_available = true;
                if (file_exists($logfile)) {
                    unlink($logfile);
                }
                $result_code = upgrade_main(   directory: $directory,
                                               verbose: $db_verbose,
                                               check_git: false,
                                               do_git: false,
                                               export_db: false,
                                               check_db: true,
                                               strict_db: false,
                                               do_db: true,
                                               force: $force,
                                               connection: false,
                                               origin: false,
                                               drop_keys: false
                );
            break;    
            case 'refresh':
                $last_action = "Anzeige aktualisiert";
            break;
            case 'save_remote':
                $last_action = "Upgrade-Quelle speichern";
            break;
            case 'rollback_to_tag':
                $last_action = "Rollback durchgeführt";
                $rollback_tag = $this->app->Secure->GetPOST('rollback_tag');
                
                if ($git_root !== "" && !empty($rollback_tag)) {
                    // Validiere Tag-Name (nur pre-upgrade-* Tags erlauben)
                    if (preg_match('/^pre-upgrade-\d{4}-\d{2}-\d{2}-\d{2}-\d{2}-\d{2}$/', $rollback_tag)) {
                        if (file_exists($logfile)) {
                            unlink($logfile);
                        }
                        
                        // Checkout zum Tag
                        $checkout_cmd = 'git -C '.escapeshellarg($git_root).' checkout '.escapeshellarg($rollback_tag).' -f 2>&1';
                        $checkout_output = shell_exec($checkout_cmd);
                        
                        file_put_contents($logfile, "=== Rollback to tag: $rollback_tag ===\n");
                        file_put_contents($logfile, $checkout_output, FILE_APPEND);
                        
                        $this->app->erp->LogFile(
                            'Rollback performed',
                            ['tag' => $rollback_tag, 'user' => $this->app->User->GetName()],
                            'upgrade',
                            'rollback'
                        );
                        
                        $status_headline = "Rollback durchgeführt";
                        $status_level = "info";
                        $status_message = "System auf Stand von Tag $rollback_tag zurückgesetzt.";
                        $guidance_title = "Wichtig";
                        $guidance_message = "Code wurde zurückgesetzt. DB-Änderungen wurden NICHT rückgängig gemacht!";
                    } else {
                        $status_headline = "Ungültiger Tag";
                        $status_level = "error";
                        $status_message = "Nur pre-upgrade-* Tags können für Rollback verwendet werden.";
                    }
                }
            break;
        }

        // Read results
        $result = file_exists($logfile) ? file_get_contents($logfile) : "";
        $highlight_force = (!$force && str_contains($result, "Clear modified files or use -f"));

        if ($result_code === 0 && $result !== "") {
            if (str_contains($result, "Aborted")) {
                $result_code = -1;
            }
        }

        if ($submit && $submit !== 'refresh' && $submit !== 'save_remote') {

            $diff_count = null;
            if (preg_match('/(\\d+) differences\\./', $result, $matches)) {
                $diff_count = (int)$matches[1];
            }

            $has_modified_files = str_contains($result, "There are modified files");

            if ($result_code === 0) {
                $status_headline = "Aktion erfolgreich";
                $status_level = "success";
                if (str_contains($result, "Already up to date")) {
                    $status_message = "Keine neuen Updates verfügbar. System ist aktuell.";
                } else {
                    $status_message = "Der Durchlauf wurde ohne Fehler abgeschlossen.";
                }

                switch ($submit) {
                    case 'check_upgrade':
                        if ($diff_count === 0) {
                            $guidance_title = "Alles aktuell";
                            $guidance_message = "Keine DB-Differenzen erkannt. Code wurde nicht aktualisiert. Kein Upgrade nötig.";
                        } elseif ($diff_count !== null && $diff_count > 0) {
                            $guidance_title = "Upgrade empfohlen";
                            $guidance_message = "Es wurden Unterschiede festgestellt. Starte jetzt \"Upgrade jetzt starten\", um Dateien und DB zu aktualisieren.";
                        } else {
                            $guidance_title = "Prüfung abgeschlossen";
                            $guidance_message = "Protokoll prüfen. Wenn Änderungen gewünscht sind, starte das Upgrade.";
                        }
                        if ($has_modified_files) {
                            $guidance_message .= " Achtung: Lokale Änderungen vorhanden – entweder bereinigen oder mit '-f/Erzwingen' arbeiten.";
                        }
                        break;
                    case 'do_upgrade':
                        $guidance_title = "Upgrade abgeschlossen";
                        $guidance_message = "System und Datenbank wurden aktualisiert. Nächster Schritt: Funktionstest durchführen.";
                        break;
                    case 'check_db':
                        if ($diff_count === 0) {
                            $guidance_title = "Datenbank ist aktuell";
                            $guidance_message = "Keine Differenzen gefunden. Kein DB-Upgrade erforderlich.";
                        } elseif ($diff_count !== null && $diff_count > 0) {
                            $guidance_title = "DB-Upgrade möglich";
                            $guidance_message = "Es wurden Datenbankunterschiede gefunden. Starte \"Datenbank-Upgrade\".";
                        } else {
                            $guidance_title = "Prüfung abgeschlossen";
                            $guidance_message = "Protokoll ansehen und bei Bedarf das DB-Upgrade auslösen.";
                        }
                        break;
                    case 'do_db_upgrade':
                        $guidance_title = "Datenbank aktualisiert";
                        $guidance_message = "DB-Upgrade ausgeführt. Prüfe das Protokoll und teste Funktionen.";
                        break;
                    default:
                        $guidance_title = "Ergebnis vorliegend";
                        $guidance_message = "Siehe Protokoll für Details.";
                }

            } elseif ($result_code === -1) {
                $status_headline = "Fehlgeschlagen";
                $status_level = "error";
                $status_message = "Upgrade hat Fehler gemeldet. Protokoll prüfen.";
                $guidance_title = "Fehlerbehebung";
                $guidance_message = "Siehe Protokoll, bereinige Fehler (z.B. lokale Änderungen, Verbindungsprobleme) und starte erneut.";
            } else {
                $status_headline = "Abgeschlossen";
                $status_level = "info";
                $status_message = "Ergebnis siehe Protokoll.";
                $guidance_title = "Protokoll prüfen";
                $guidance_message = "Bitte Protokoll ansehen und ggf. nächsten Schritt manuell wählen.";
            }
        }

        if ($highlight_force && $result_code === -1) {
            $status_level = "warning";
            $status_headline = "Lokale Dateien verändert";
            $status_message = "Es gibt lokale Änderungen im Repo. Bitte 'Erzwingen (-f)' aktivieren oder Änderungen bereinigen.";
            $guidance_title = "Hinweis";
            $guidance_message = "Aktiviere unten 'Erzwingen (-f)' und starte das Upgrade erneut (oder setze die Änderungen zurück).";
        }

        if ($result !== "") {
            $last_run = date('d.m.Y H:i', filemtime($logfile));
        } else {
            $result = "Noch kein Protokoll vorhanden.";
            $last_run = "Noch kein Durchlauf";
        }

        $this->app->Tpl->Set('STATUS_HEADLINE', $status_headline);
        $this->app->Tpl->Set('STATUS_LEVEL', $status_level);
        $this->app->Tpl->Set('STATUS_MESSAGE', $status_message);
        $this->app->Tpl->Set('GUIDANCE_TITLE', $guidance_title);
        $this->app->Tpl->Set('GUIDANCE_MESSAGE', $guidance_message);
        $this->app->Tpl->Set('LAST_ACTION', $last_action);
        $this->app->Tpl->Set('LAST_RUN', $last_run);
        $this->app->Tpl->Set('UPGRADE_BUTTON_ACTION', $upgrade_available ? "do_upgrade" : "check_upgrade");
        $this->app->Tpl->Set('UPGRADE_BUTTON_LABEL', $upgrade_available ? "Upgrade starten" : "Upgrades prüfen");
        $this->app->Tpl->Set('UPGRADE_FORCE_VISIBLE', ($upgrade_available || $highlight_force) ? "" : "hidden");
        $this->app->Tpl->Set('FORCE_HIGHLIGHT_CLASS', $highlight_force ? "force-highlight" : "");
        $this->app->Tpl->Set('UPGRADE_DB_BUTTON_ACTION', $upgrade_db_available ? "do_db_upgrade" : "check_db");
        $this->app->Tpl->Set('UPGRADE_DB_BUTTON_LABEL', $upgrade_db_available ? "DB-Upgrade" : "DB prüfen");
        $this->app->Tpl->Set('UPGRADE_DB_FORCE_VISIBLE', "hidden");

        // Rollback-Tags laden
        $rollback_tags = [];
        $rollback_tags_html = "";
        if ($git_root !== "") {
            $tags_output = shell_exec('git -C '.escapeshellarg($git_root).' tag -l "pre-upgrade-*" --sort=-creatordate 2>&1');
            if ($tags_output !== null) {
                $tags = array_filter(explode("\n", trim($tags_output)));
                $rollback_tags = array_slice($tags, 0, 10); // Nur letzte 10 Tags
                
                if (!empty($rollback_tags)) {
                    $rollback_tags_html .= '<select name="rollback_tag" class="input-inline" style="margin-bottom:8px;">';
                    foreach ($rollback_tags as $tag) {
                        $rollback_tags_html .= '<option value="'.htmlspecialchars($tag).'">'.htmlspecialchars($tag).'</option>';
                    }
                    $rollback_tags_html .= '</select>';
                }
            }
        }
        
        $has_rollback_tags = !empty($rollback_tags);
        $this->app->Tpl->Set('ROLLBACK_TAGS_SELECT', $rollback_tags_html);
        $this->app->Tpl->Set('ROLLBACK_VISIBLE', $has_rollback_tags ? "" : "hidden");
        $this->app->Tpl->Set('LAST_ROLLBACK_TAG', $_SESSION['last_rollback_tag'] ?? '');

        $this->app->Tpl->Set('CURRENT', $this->app->erp->Revision());
        $revision_raw = (string)$this->app->erp->Revision();
        $app_version = trim((string)preg_replace('/\\s*\\([^)]*\\)\\s*$/', '', $revision_raw));
        if ($app_version === '') {
            $app_version = $revision_raw;
        }
        $this->app->Tpl->Set('APP_VERSION', htmlspecialchars($app_version));
        $this->app->Tpl->Set('OUTPUT_FROM_CLI',nl2br($result));
        $this->app->Tpl->Parse('PAGE', "upgrade.tpl");
    }   
    

}
