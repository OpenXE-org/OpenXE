<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
    <div class="rTabs"><ul class="">
  <li class="[AKTIVAUFGABENLISTE]"><a href="index.php?module=aufgaben&action=list&cmd=aufgabenliste&sid=[MITARBEITER]">{|Aufgabenliste|}</a></li>
  <li class="[AKTIVKALENDER]"><a href="index.php?module=aufgaben&action=list&cmd=kalender&sid=[MITARBEITER]">{|Kalender|}</a></li>
        <!--<li class="[ACTIVEPROJECTS]"><a href="index.php?module=aufgaben&action=list&cmd=projects&sid=[MITARBEITER]">{|Projekte / Teilprojekte|}</a></li>-->
</ul>

<div class="rTabSelect">[RTABSELECT]</div><div class="clear"></div></div>
  <div class="row">
    <div class="row-height">
      <div class="col-xs-12 col-md-10 col-md-height">
        <div class="inside_white inside-full-height">
          [ANZEIGE]
        </div>
      </div>
      <div class="col-xs-12 col-md-2 col-md-height">
        <div class="inside_white inside-full-height">
          <fieldset><legend>{|Aktionen|}</legend>
            <input type="button" class="button button-primary button-block" onclick="AufgabenEdit(0);" value="✚ {|Neue Aufgabe|}" />
            [BEFOREPROJECTCREATE]
            <input type="button" class="button button-secondary button-block" id="createproject" value="✚ {|Neues Projekt|}" />
            [AFTERPROJECTCREATE]
            [BEFOREPROJECTDASHBOARD]
            <input type="button" class="button button-secondary button-block" id="createsubproject" value="✚ {|Neues Teilprojekt|}" />
            [AFTERPROJECTDASHBOARD]
          </fieldset>
        </div>
      </div>
    </div>
  </div>
</div>

</div>
[AUFGABENPOPUP]

<div id="createprojectpopup">
  <div id="createprojectmessage"></div>
  [CREATEPROJECTFIELDSET]
  [EMPLOYETABLE]
</div>
<div id="createsubprojectpopup">
  <div id="createsubprojectmessage"></div>
  <fieldset>
    <legend>{|Teilprojekt|}</legend>
    <table>
      <tr>
        <td><label for="subprojectproject">{|Projekt|}:</label></td>
        <td><input type="text" id="subprojectproject" name="subprojectproject" size="40" /></td>
      </tr>
      <tr>
        <td>
          <label for="subprojecttitle">{|Teilprojekt|}:</label>
        </td>
        <td><input type="text" id="subprojecttitle" name="subprojecttitle" size="40" /></td>
      </tr>
      <tr>
        <td>
          <label for="subprojectdescription">{|Beschreibung|}:</label>
        </td>
        <td><textarea id="subprojectdescription" name="subprojectdescription"></textarea></td>
      </tr>
      <tr>
        <td><label for="subprojectstatus">{|Status|}:</label></td>
        <td><select id="subprojectstatus" name="subprojectstatus">[STATUSSEL]</select></td>
      </tr>
      <tr>
        <td><label for="subprojectparent">{|Vorgänger|}:</label></td>
        <td><select id="subprojectparent" name="subprojectparent"></select></td>
      </tr>
      <tr>
        <td><label for="subprojectposition">{|Position|}:</label></td>
        <td>
          <label for="subprojectpositionneighbour">{|Nach|}:</label>
          <input type="radio" id="subprojectpositionneighbour" value="postypnachbar" name="subprojectposition" />
          <label for="subprojectpositionchild">{|Unterprojekt|}:</label>
          <input type="radio" id="subprojectpositionchild" value="postypkind" name="subprojectposition" />
        </td>
      </tr>

      <tr>
        <td>
          <label for="subprojectstartdate">{|Start Datum|}:</label>
        </td>
        <td><input type="text" size="15" id="subprojectstartdate" name="subprojectstartdate" /></td>
      </tr>
      <tr>
        <td>
          <label for="subprojectenddate">{|Abgabe Datum|}:</label>
        </td>
        <td><input type="text" size="15" id="subprojectenddate" name="subprojectenddate" /></td>
      </tr>
      <tr>
        <td>
          <label for="subprojectleader">{|Verantwortlicher|}:</label>
        </td>
        <td><input type="text" size="40" id="subprojectleader" name="subprojectleader" /></td>
      </tr>
      <tr>
        <td>
          <label for="subprojectcolor">{|Farbe|}:</label>
        </td>
        <td><input type="text" size="15" id="subprojectcolor" name="subprojectcolor" /></td>
      </tr>

    </table>
  </fieldset>
  [SUBPROJECTEMPLOYETABLE]
</div>
