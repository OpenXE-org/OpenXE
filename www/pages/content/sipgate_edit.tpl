<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
[TAB1]
<!--
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-sm-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Status|}</legend>
<form id="meiappsform" action="" method="post" enctype="multipart/form-data">
<div class="info">Das letzte Backup wurde am 24.12.2001 um 06:12 erfolgreich durchgeführt.</div>
</form>

</fieldset>
</div>
</div>
</div>
</div>
-->


<div class="row">
<div class="row-height">


<div class="col-xs-12 col-md-4 col-md-height">
    <div class="inside inside-full-height">
        <fieldset>
            <legend>{|Zugangsdaten|}</legend>
            <form action="" method="post" id="sipgate_user_form">
                <input name="type" value="credentials" type="hidden">
                <table>
                    <tr>
                        <td width="150">Username:</td>
                        <td><input id="api-user"
                                   name="api-user"
                                   value="[USER]"
                                   placeholder="Username"
                                   size="50"
                                   autocomplete="off"
                                   type="text">
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td width="150">Passwort:</td>
                        <td><input id="api-key"
                                   name="api-key"
                                   value="[KEY]"
                                   placeholder="Passwort"
                                   size="50"
                                   autocomplete="off"
                                   type="password">
                        </td>
                    </tr>
                    <tr>
                        <td width="150"></td>
                        <td>
                            <a id="api-test">Zugangsdaten testen</a>
                            <span id="api-test-result"></span>
                        </td>
                    </tr>
                    <tr>
                        <td width="150"></td>
                        <td>
                            <input name="submit" value="Speichern" type="submit">
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>

        <fieldset>
            <legend>{|Sipgate.io|}</legend>
            <form action="" method="post" id="sipgate_io_form">
                <input name="type" value="webhook" type="hidden">
                [SIPGATE_IO_ERROR]
                <table id="sipgate_io_table" class="collapsed">
                    <tr>
                        <td width="150">Webhook URL:</td>
                        <td><input id="sipgate_webhook"
                                   name="sipgate-webhook"
                                   value="[WEBHOOK]"
                                   placeholder="Webhook"
                                   size="50"
                                   autocomplete="off"
                                   readonly="readonly"
                                   type="text">
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td width="150">Proxy URL:</td>
                        <td><input id="sipgate_proxy"
                                   name="sipgate-proxy"
                                   value="[SIPGATE_PROXY]"
                                   placeholder="Proxy URL"
                                   size="50"
                                   autocomplete="off"
                                   type="text">
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td width="150"></td>
                        <td>
                            <input name="submit" value="Speichern" type="submit">
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
    </div>
</div>

<div class="col-xs-12 col-md-8 col-md-height">
    <div class="inside inside-full-height">
        <fieldset><legend>{|USERS|}</legend>
            [USERS_ERROR]
            <table class="mkTable">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
                [USERS]
            </table>
        </fieldset>
    </div>
</div>

</div>
</div>

[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>

