<div class="container-fluid" id="sendcloudapp">
    <form action="" method="post" v-on:submit.prevent="submit">
        <div class="row">
            <div v-for="msg in messages" :class="msg.class">{{msg.text}}</div>
            <div>
                <h1>{|Paketmarken Drucker f&uuml;r|} SendCloud</h1>
            </div>
            <div class="col-md-4">
                <h2>{|Empf&auml;nger|}</h2>
                <table>
                    <tr>
                        <td>{|Name|}:</td>
                        <td><input type="text" size="36" v-model="form.l_name"></td>
                    </tr>
                    <tr>
                        <td>{|Firmenname|}:</td>
                        <td><input type="text" size="36" v-model="form.l_companyname"></td>
                    </tr>
                    <tr>
                        <td>{|Strasse/Hausnummer|}:</td>
                        <td>
                            <input type="text" size="30" v-model="form.strasse">
                            <input type="text" size="5" v-model="form.hausnummer">
                        </td>
                    </tr>
                    <tr>
                        <td>{|Adresszeile 2|}:</td>
                        <td><input type="text" size="36" v-model="form.l_address2"></td>
                    </tr>
                    <tr>
                        <td>{|PLZ/Ort|}:</td>
                        <td><input type="text" size="5" v-model="form.plz">
                            <input type="text" size="30" v-model="form.ort">
                        </td>
                    </tr>
                    <tr>
                        <td>{|Bundesland|}:</td>
                        <td><input type="text" size="36" v-model="form.bundesland"></td>
                    </tr>
                    <tr>
                        <td>{|Land|}:</td>
                        <td>
                            <select v-model="form.land">
                                <option v-for="(value, key) in countries" :value="key">{{value}}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{|E-Mail|}:</td>
                        <td><input type="text" size="36" v-model="form.email"></td>
                    </tr>
                    <tr>
                        <td>{|Telefon|}:</td>
                        <td><input type="text" size="36" v-model="form.telefon"></td>
                    </tr>

                </table>
            </div>
            <div class="col-md-4" v-once>
                <h2>vollst. Adresse</h2>
                <table>
                    <tr>
                        <td>{|Name|}</td>
                        <td>{{form.name}}</td>
                    </tr>
                    <tr>
                        <td>{|Ansprechpartner|}</td>
                        <td>{{form.ansprechpartner}}</td>
                    </tr>
                    <tr>
                        <td>{|Abteilung|}</td>
                        <td>{{form.abteilung}}</td>
                    </tr>
                    <tr>
                        <td>{|Unterabteilung|}</td>
                        <td>{{form.unterabteilung}}</td>
                    </tr>
                    <tr>
                        <td>{|Adresszusatz|}</td>
                        <td>{{form.adresszusatz}}</td>
                    </tr>
                    <tr>
                        <td>{|Strasse|}</td>
                        <td>{{form.streetwithnumber}}</td>
                    </tr>
                    <tr>
                        <td>{|PLZ/Ort|}</td>
                        <td>{{form.plz}} {{form.ort}}</td>
                    </tr>
                    <tr>
                        <td>{|Bundesland|}</td>
                        <td>{{form.bundesland}}</td>
                    </tr>
                    <tr>
                        <td>{|Land|}</td>
                        <td>{{form.land}}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
                <h2>{|Paket|}</h2>
                <table>
                    <tr>
                        <td>{|Gewicht (in kg)|}:</td>
                        <td><input type="text" v-model="form.weight"></td>
                    </tr>
                    <tr>
                        <td>{|H&ouml;he (in cm)|}:</td>
                        <td><input type="text" size="10" v-model="form.height"></td>
                    </tr>
                    <tr>
                        <td>{|Breite (in cm)|}:</td>
                        <td><input type="text" size="10" v-model="form.width"></td>
                    </tr>
                    <tr>
                        <td>{|L&auml;nge (in cm)|}:</td>
                        <td><input type="text" size="10" v-model="form.length"></td>
                    </tr>
                    <tr>
                        <td>{|Produkt|}:</td>
                        <td>
                            <select v-model="form.method">
                                <option v-for="(value, key, index) in methods" :value="key">{{value}}</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12">
                <h2>{|Bestellung|}</h2>
                <table>
                    <tr>
                        <td>{|Bestellnummer|}:</td>
                        <td><input type="text" size="36" v-model="form.order_number"></td>
                    </tr>
                    <tr>
                        <td>{|Rechnungsnummer|}:</td>
                        <td><input type="text" size="36" v-model="form.invoice_number"></td>
                    </tr>
                    <tr>
                        <td>{|Sendungsart|}:</td>
                        <td>
                            <select v-model="form.sendungsart">
                                <option v-for="(value, key) in customs_shipment_types" :value="key">{{value}}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{|Versicherungssumme|}:</td>
                        <td><input type="text" size="10" v-model="form.total_insured_value"/></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-12">
                <table>
                    <tr>
                        <th>{|Bezeichnung|}</th>
                        <th>{|Menge|}</th>
                        <th>{|HSCode|}</th>
                        <th>{|Herkunftsland|}</th>
                        <th>{|Einzelwert|}</th>
                        <th>{|Einzelgewicht|}</th>
                        <th>{|Währung|}</th>
                        <th>{|Gesamtwert|}</th>
                        <th>{|Gesamtgewicht|}</th>
                        <th><a v-on:click="addPosition"><img src="themes/new/images/add.png"></a></</th>
                    </tr>
                    <tr v-for="(pos, index) in form.positions">
                        <td><input type="text" v-model="pos.bezeichnung" required></td>
                        <td><input type="text" v-model="pos.menge" required></td>
                        <td><input type="text" v-model="pos.zolltarifnummer" required></td>
                        <td><input type="text" v-model="pos.herkunftsland" required></td>
                        <td><input type="text" v-model="pos.zolleinzelwert" required></td>
                        <td><input type="text" v-model="pos.zolleinzelgewicht" required></td>
                        <td><input type="text" v-model="pos.zollwaehrung" required></td>
                        <td>{{Number(pos.menge*pos.zolleinzelwert || 0).toFixed(2)}}</td>
                        <td>{{Number(pos.menge*pos.zolleinzelgewicht || 0).toFixed(3)}}</td>
                        <td><a v-on:click="deletePosition(index)"><img src="themes/new/images/delete.svg"></a></td>
                    </tr>
                    <tr>
                        <td colspan="7"></td>
                        <td>{{total_value.toFixed(2)}}</td>
                        <td>{{total_weight.toFixed(3)}}</td>
                    </tr>
                </table>
            </div>
            <div>
                <input class="btnGreen" type="submit" value="{|Paketmarke drucken|}" name="drucken">&nbsp;
                <input type="button" value="{|Andere Versandart auswählen|}" name="anders">&nbsp;
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    const sendcloudApp = new Vue({
        el: '#sendcloudapp',
        data: {
            form: [JSON],
            countries: [JSON_COUNTRIES],
            methods: [JSON_METHODS],
            customs_shipment_types: [JSON_CUSTOMS_SHIPMENT_TYPES],
            messages: []
        },
        computed: {
            total_value() {
                let sum = 0;
                for(const pos of this.form.positions) {
                    sum += pos.menge * pos.zolleinzelwert;
                }
                return sum;
            },
            total_weight() {
                let sum = 0;
                for(const pos of this.form.positions) {
                    sum += pos.menge * pos.zolleinzelgewicht;
                }
                return sum;
            }
        },
        methods: {
            submit: function() {
                let app = this;
                let xhr = new XMLHttpRequest();
                xhr.open('POST', location.href, true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.onload = function () {
                    let json = JSON.parse(this.response);
                    app.messages = json.messages;
                }
                xhr.send(JSON.stringify($.extend({submit:'print'}, this.form)));
            },
            addPosition: function() {
                this.form.positions.push({ });
            },
            deletePosition: function(index) {
                this.form.positions.splice(index, 1);
            }
        }
    })
</script>