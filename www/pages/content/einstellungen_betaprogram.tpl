<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>

    <div id="tabs-1">
        [MESSAGE]
        [BEFORESHOWIFBETAACTIVATED]
        <form method="post" action="index.php?module=einstellungen&action=betaprogram">
            <div class="info">{|Du bist erfolgreich am Beta Programm in xentral angemeldet.|}
                <input type="hidden" name="deactivate" value="1" />
                <input  type="submit" class="button button-primary" value="Vom Beta Programm abmelden" />
            </div>
        </form>
        [AFTERSHOWIFBETAACTIVATED]
        <div class="row">
            <div class="row-height">
                <div class="col-xs-12 col-md-12 col-md-height">
                    <div class="inside inside-full-height">
                        <fieldset>
                            <legend>Beta Programm</legend>
                                <p>Gerne ziehen wir Sie auf die neueste Version um. Diese Version befindet sich gegenwärtig in der Beta-Phase.
                                <br>Machen Sie sich daher bitte zuerst damit vertraut, was der Umzug auf eine Beta-Version beinhaltet.</p>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="row-height">
                    <div class="col-xs-12 col-md-12 col-md-height">
                        <div class="inside inside-full-height">
                            <fieldset>
                                <legend>Was bedeutet "Beta-Phase"?</legend>
                                <p>Die Beta-Version befindet sich noch vor dem offiziellen Versionsrelease.
                                   Eine Beta-Version ist kurz vor Fertigstellung,
                                   jedoch noch nicht in allen Modulen vollständig getestet. Es können weiterhin Änderungen und Fixes hinzukommen.
                                   Daher nehmen wir das Feedback unserer Kunden, die mit einer Beta-Version arbeiten gerne auf und setzen es je
                                   nach Möglichkeit und Einstufung im Xentral Team um. Wie es in einer Software üblich ist, kann es dadurch in
                                   sehr seltenen Fällen zu weiteren Unstimmigkeiten kommen, die wir im Nachgang natürlich so schnell wie möglich
                                   beheben werden.</p>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
            [BEFORESHOWIFBETADEACTIVATED]
            <div class="row">
                <div class="row-height">
                    <div class="col-xs-12 col-md-12 col-md-height">
                        <div class="inside inside-full-height">
                            <fieldset>
                                <legend>Wann macht es Sinn auf eine Beta-Version zu wechseln?</legend>
                                <p>Sie sind noch in der Einrichtungsphase und
                                   benötigen ein neues Feature um mit Ihrem Workflow live zu gehen. Sie melden uns eventuell auftretende
                                   Unstimmigkeiten per Support-Ticketsystem. Sie arbeiten schon produktiv, benötigen einen Bugfix oder ein
                                   neues Feature und erklären sich bereit, uns eventuell auftretende Unstimmigkeiten zu melden. Sie sind ein
                                   Early Adopter und bei der Xentral Beta-Version immer mit dabei, beziehungsweise Sie haben Ihr eigenes
                                   Testsystem und möchten die neue Version ausprobieren. Bitte geben Sie uns eine kurze Rückmeldung, ob Sie
                                   den Bedingungen der Beta-Version zustimmen. Wir geben Ihnen direkt Feedback
                                   über die Möglichkeiten einer Freischaltung.</p>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
            [AFTERSHOWIFBETADEACTIVATED]
            <br />
            [BEFORESHOWIFBETADEACTIVATED]
            <form method="post" action="index.php?module=einstellungen&action=betaprogram">
                <table class="pull-right">
                    <tr>
                        <td><input type="checkbox" id="beta-agreement" name="beta-agreement" />
                            <label for="beta-agreement">Ich habe verstanden und möchte am Beta Programm teilnehmen</label></td>
                        <td><button type="submit" class="button button-primary">Am Beta Programm teilnehmen</button></td>
                    </tr>
                </table>
            </form>
            [AFTERSHOWIFBETADEACTIVATED]
        </div>
    </div>
</div>

<div id="beta-program-thanks-popup" data-open="[BETAOPEN]" class="hide">
    <div class="beta-program thanks hide">
        <div class="beta-program-highlight">
            <svg width="39" height="38" viewBox="0 0 39 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M21.96 24.8784C21.96 27.355 18.7933 30.6667 18.7933 30.6667C18.7933 30.6667 15.6267 27.355 15.6267 24.8784C15.5453 23.6978 16.129 22.5703 17.14 21.9552C18.151 21.3401 19.4206 21.3401 20.4317 21.9552C21.4427 22.5703 22.0264 23.6978 21.945 24.8784H21.96Z" stroke="#5B64EE" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M22.1332 6.5C22.1332 3.73833 18.7999 1.5 18.7999 1.5C18.7999 1.5 15.4666 3.73833 15.4666 6.5V18.1667H22.1332V6.5Z" stroke="#5B64EE" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M15.4665 18.1666H11.4099C11.1525 18.1664 10.9097 18.0473 10.752 17.844C10.5943 17.6407 10.5394 17.3759 10.6032 17.1266C11.3214 14.8165 13.1463 13.0179 15.4665 12.3333" stroke="#5B64EE" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22.1333 18.1666H26.19C26.4476 18.1669 26.6909 18.0481 26.849 17.8447C27.0071 17.6412 27.0622 17.3762 26.9983 17.1266C26.2771 14.8177 24.4526 13.0201 22.1333 12.3333" stroke="#5B64EE" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M13.8 21.5001C13.1266 21.4124 12.4673 21.742 12.1334 22.3334C11.9995 22.5951 11.9995 22.9051 12.1334 23.1668H11.3C9.9359 23.206 8.83924 24.3026 8.80005 25.6668" stroke="#5B64EE" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M23.8 21.5001C24.4735 21.4124 25.1328 21.742 25.4667 22.3334C25.6014 22.5949 25.6014 22.9053 25.4667 23.1668H26.3C27.6646 23.2051 28.7617 24.3023 28.8 25.6668" stroke="#5B64EE" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M31.2998 28.1667C31.9814 28.1073 32.6404 28.4275 33.0148 29L37.1332 34C37.3865 34.5566 37.3343 35.2047 36.9951 35.7135C36.6558 36.2224 36.0778 36.5198 35.4665 36.5H2.93485C2.32563 36.518 1.75087 36.218 1.41727 35.7079C1.08368 35.1978 1.03921 34.551 1.29985 34L5.38318 29C5.6803 28.4854 6.22895 28.1679 6.82318 28.1667" stroke="#5B64EE" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12.1315 8.16675H7.13149C6.21102 8.16675 5.46482 8.91294 5.46482 9.83341V26.5001C5.46482 27.4201 5.37816 28.1667 6.29816 28.1667H12.1315" stroke="#5B64EE" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M25.4666 28.1667H31.2999C32.2216 28.1667 32.1332 27.4201 32.1332 26.5001V9.83341C32.1332 8.91294 31.387 8.16675 30.4666 8.16675H25.4666" stroke="#5B64EE" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16.3 33.1667H21.3" stroke="#5B64EE" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <p>In der neuen Beta-Version haben wir viele interessante Dinge für Euch vorbereitet.</p>
            <a href="#" id="beta-program-close-btn" class="button button-primary">OK</a>
        </div>
    </div>
</div>