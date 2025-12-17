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

        $project_root = dirname(__DIR__, 2);
        $git_branch = "";
        $git_commit = "";
        if (is_dir($project_root."/.git")) {
            $git_branch = trim((string)@shell_exec('cd '.escapeshellarg($project_root).' && git rev-parse --abbrev-ref HEAD'));
            $git_commit = trim((string)@shell_exec('cd '.escapeshellarg($project_root).' && git log -1 --date=short --pretty="%h | %cd"'));
        }
        $branch_alignment_text = "Lokaler Branch entspricht der Upgrade-Quelle.";
        $branch_alignment_class = "pill-success";

        if (is_readable($remote_config_file)) {
            $remote_data_raw = file_get_contents($remote_config_file);
            $remote_data = json_decode($remote_data_raw, true) ?: array();
            $remote_host = $remote_data['host'] ?? "";
            $remote_branch = $remote_data['branch'] ?? "";
        } else {
            $status_headline = "Hinweis";
            $status_level = "warning";
            $status_message = "Konfiguration der Upgrade-Quelle konnte nicht geladen werden.";
        }

        if ($git_branch !== "" && $remote_branch !== "" && $git_branch !== $remote_branch) {
            $branch_alignment_text = "Achtung: Lokaler Branch (".$git_branch.") weicht von Upgrade-Quelle (".$remote_branch.") ab.";
            $branch_alignment_class = "pill-warning";
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
                    array('host' => $remote_host_input, 'branch' => $remote_branch_input),
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                );
                if (file_put_contents($remote_config_file, $payload) === false) {
                    $remote_errors[] = "Upgrade-Quelle konnte nicht gespeichert werden.";
                } else {
                    $remote_host = $remote_host_input;
                    $remote_branch = $remote_branch_input;
                    $status_headline = "Upgrade-Quelle gespeichert";
                    $status_level = "success";
                    $status_message = "Remote und Branch wurden übernommen.";
                }
            } else {
                $status_headline = "Eingabefehler";
                $status_level = "error";
                $status_message = implode(" ", $remote_errors);
            }
        }

        $this->app->Tpl->Set('REMOTE_HOST', htmlspecialchars($remote_host));
        $this->app->Tpl->Set('REMOTE_BRANCH', htmlspecialchars($remote_branch));
        $this->app->Tpl->Set('LOCAL_BRANCH', htmlspecialchars($git_branch));
        $this->app->Tpl->Set('LOCAL_COMMIT', htmlspecialchars($git_commit));
        $this->app->Tpl->Set('BRANCH_ALIGNMENT', htmlspecialchars($branch_alignment_text));
        $this->app->Tpl->Set('BRANCH_ALIGNMENT_CLASS', $branch_alignment_class);

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
        }

        // Read results
        $result = file_exists($logfile) ? file_get_contents($logfile) : "";

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
        $this->app->Tpl->Set('UPGRADE_FORCE_VISIBLE', $upgrade_available ? "" : "hidden");
        $this->app->Tpl->Set('UPGRADE_DB_BUTTON_ACTION', $upgrade_db_available ? "do_db_upgrade" : "check_db");
        $this->app->Tpl->Set('UPGRADE_DB_BUTTON_LABEL', $upgrade_db_available ? "DB-Upgrade" : "DB prüfen");
        $this->app->Tpl->Set('UPGRADE_DB_FORCE_VISIBLE', "hidden");

        $this->app->Tpl->Set('CURRENT', $this->app->erp->Revision());
        $this->app->Tpl->Set('OUTPUT_FROM_CLI',nl2br($result));
        $this->app->Tpl->Parse('PAGE', "upgrade.tpl");
    }   
    

}
