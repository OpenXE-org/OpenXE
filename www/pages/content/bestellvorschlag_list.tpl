<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Allgemein</a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE]
        <form action="" method="post">
             <div class="row">
            	<div class="row-height">
            		<div class="col-xs-14 col-md-4 col-md-height">
            			<div class="inside inside-full-height">
                           <fieldset>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <legend>{|Einstellungen|}</legend>
                                        <td>{|Absatz ber&uuml;cksichtigen (Monate)|}:</td>
                                        <td><input type="number" min="0" name="monate_absatz" id="monate_absatz" value="[MONATE_ABSATZ]" size="20"></td>
                                    </tr>
                                    <tr>
                                        <td>{|Vorausplanen (Monate)|}:</td>
                                        <td><input type="number" min="0" name="monate_voraus" id="monate_voraus" value="[MONATE_VORAUS]" size="20"></td>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
               		</div>
                    <div class="col-xs-14 col-md-8 col-md-height">
            			<div class="inside inside-full-height">
                            <fieldset>
                                <legend>{|Filter|}</legend>
                            </fieldset>
                            <fieldset>
                                <div class="filter-box filter-usersave">
                                    <div class="filter-block filter-inline">
                                        <ul class="filter-list">
                                            <li class="filter-item" title="Mindestlagermengen ber&uuml;cksichtigen">
                                              <label for="mindestlager" class="switch">
                                                <input type="checkbox" id="mindestlager">
                                                <span class="slider round"></span>
                                              </label>
                                              <label for="mindestlager">{|Mindestlager|}</label>
                                            </li>
                                            <li class="filter-item" title="Belegentw&uuml;rfe berücksichtigen">
                                              <label for="entwuerfe" class="switch">
                                                <input type="checkbox" id="entwuerfe"">
                                                <span class="slider round"></span>
                                              </label>
                                              <label for="entwuerfe">{|Entw&uuml;rfe|}</label>
                                            </li>
                                            <li class="filter-item" title="Auftr&auml;ge mit Reserviersperre berücksichtigen">
                                              <label for="reserviersperre" class="switch">
                                                <input type="checkbox" id="reserviersperre"">
                                                <span class="slider round"></span>
                                              </label>
                                              <label for="reserviersperre">{|inkl. Reserviersperre|}</label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="filter-box filter-usersave">
                                    <div class="filter-block filter-inline">
                                        [KATEGORIENFILTER]
                                    </div>
                                </div>
                            </fieldset>
                        </div>
               		</div>
                    <div class="col-xs-14 col-md-2 col-md-height">
            			<div class="inside inside-full-height">
                           <fieldset>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <legend>Aktionen</legend>
                                    <tr>
                                        <td><button name="submit" class="ui-button-icon" style="width:100%;" value="speichern">{|Speichern|}</button></td>
                                    </tr>
                                    <tr>
                                        <td><button name="submit" class="ui-button-icon" style="width:100%;" value="loeschen">{|Zur&uuml;cksetzen|}</button></td>
                                    </tr>
                                    <tr>
                                        <td><button name="submit" class="ui-button-icon" style="width:100%;" value="bestellungen_erzeugen">{|Bestellungen erzeugen|}</button></td>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
               		</div>
               	</div>	
            </div>
            <div class="row">
            	<div class="row-height">
            		<div class="col-xs-14 col-md-6 col-md-height">
            			<div class="inside inside-full-height">
                           [TAB1]
                            <fieldset>
                                <table>
                                    <tr>
                                        <td>
                                            <input type="checkbox" value="1" id="autoalle" />&nbsp;alle markieren&nbsp;
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
               		</div>
               	</div>	
            </div>
        </form>
        [TAB1NEXT]
    </div>
</div>

    <script>

        $('#autoalle').on('change',function(){
          var wert = $(this).prop('checked');
          $('#bestellvorschlag_list').find('input[type="checkbox"]').prop('checked',wert);
          $('#bestellvorschlag_list').find('input[type="checkbox"]').first().trigger('change');
        });

    </script>



           
