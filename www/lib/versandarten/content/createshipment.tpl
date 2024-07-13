<!--
SPDX-FileCopyrightText: 2022 Andreas Palm

SPDX-License-Identifier: LicenseRef-EGPL-3.1
-->

<div class="container-fluid" id="createshipmentapp">
    <form action="" method="post" v-on:submit.prevent="submit">
        <div class="row">
            <div v-for="msg in messages" :class="msg.class">{{msg.text}}</div>
            <div>
                <h1>{|Paketmarken Drucker f&uuml;r|} [CARRIERNAME]</h1>
            </div>
            <div class="col-md-4">
                <h2>{|Empf&auml;nger|}</h2>
                <table>
                    <tr>
                        <td>{|Adresstyp|}:</td>
                        <td>
                            <select v-model.number="form.addresstype">
                                <option value="0">Firma</option>
                                <option value="1">Packstation</option>
                                <option value="2">Filiale</option>
                                <option value="3">Privatadresse</option>
                            </select>
                        </td>
                    </tr>
                    <tr v-if="form.addresstype === 0">
                        <td>{|Firma|}:</td>
                        <td><input type="text" size="36" v-model.trim="form.company_name"></td>
                    </tr>
                    <tr v-if="form.addresstype === 0">
                        <td>{|Abteilung|}:</td>
                        <td><input type="text" size="36" v-model.trim="form.company_division"></td>
                    </tr>
                    <tr v-if="form.addresstype === 3">
                        <td>{|Name|}:</td>
                        <td><input type="text" size="36" v-model.trim="form.name"></td>
                    </tr>
                    <tr v-if="form.addresstype === 0 || form.addresstype === 3">
                        <td>{|Ansprechpartner|}:</td>
                        <td><input type="text" size="36" v-model.trim="form.contact_name"></td>
                    </tr>
                    <tr v-if="form.addresstype === 1 || form.addresstype === 2">
                        <td>{|Postnummer|}:</td>
                        <td><input type="text" size="36" v-model.trim="form.postnumber"></td>
                    </tr>
                    <tr v-if="form.addresstype === 0 || form.addresstype === 3">
                        <td>{|Strasse/Hausnummer|}:</td>
                        <td>
                            <input type="text" size="30" v-model.trim="form.street">
                            <input type="text" size="5" v-model.trim="form.streetnumber">
                        </td>
                    </tr>
                    <tr v-if="form.addresstype === 1">
                        <td>{|Packstationsnummer|}:</td>
                        <td><input type="text" size="10" v-model.trim="form.parcelstationNumber"></td>
                    </tr>
                    <tr v-if="form.addresstype === 2">
                        <td>{|Postfilialnummer|}:</td>
                        <td><input type="text" size="10" v-model.trim="form.postofficeNumber"></td>
                    </tr>
                    <tr v-if="form.addresstype === 0 || form.addresstype === 3">
                        <td>{|Adresszeile 2|}:</td>
                        <td><input type="text" size="36" v-model.trim="form.address2"></td>
                    </tr>
                    <tr>
                        <td>{|PLZ/Ort|}:</td>
                        <td><input type="text" size="5" v-model.trim="form.zip">
                            <input type="text" size="30" v-model.trim="form.city">
                        </td>
                    </tr>
                    <tr>
                        <td>{|Bundesland|}:</td>
                        <td><input type="text" size="36" v-model.trim="form.state"></td>
                    </tr>
                    <tr>
                        <td>{|Land|}:</td>
                        <td>
                            <select v-model="form.country" required>
                                <option v-for="(value, key) in countries" :value="key">{{value.name}}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{|E-Mail|}:</td>
                        <td><input type="text" size="36" v-model.trim="form.email"></td>
                    </tr>
                    <tr>
                        <td>{|Telefon|}:</td>
                        <td><input type="text" size="36" v-model.trim="form.phone"></td>
                    </tr>

                </table>
            </div>
            <div class="col-md-4" v-once>
                <h2>vollst. Adresse</h2>
                <table>
                    <tr>
                        <td>{|Name|}</td>
                        <td>{{form.original.name}}</td>
                    </tr>
                    <tr>
                        <td>{|Ansprechpartner|}</td>
                        <td>{{form.original.ansprechpartner}}</td>
                    </tr>
                    <tr>
                        <td>{|Abteilung|}</td>
                        <td>{{form.original.abteilung}}</td>
                    </tr>
                    <tr>
                        <td>{|Unterabteilung|}</td>
                        <td>{{form.original.unterabteilung}}</td>
                    </tr>
                    <tr>
                        <td>{|Adresszusatz|}</td>
                        <td>{{form.original.adresszusatz}}</td>
                    </tr>
                    <tr>
                        <td>{|Strasse|}</td>
                        <td>{{form.original.strasse}}</td>
                    </tr>
                    <tr>
                        <td>{|PLZ/Ort|}</td>
                        <td>{{form.original.plz}} {{form.original.ort}}</td>
                    </tr>
                    <tr>
                        <td>{|Bundesland|}</td>
                        <td>{{form.original.bundesland}}</td>
                    </tr>
                    <tr>
                        <td>{|Land|}</td>
                        <td>{{form.original.land}}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
                <h2>{|Paket|}</h2>
                <table>
                    <tr>
                        <td>{|Gewicht (in kg)</b>|}:</td>
                        <td><input type="text" v-model.number="form.weight"></td>
                    </tr>
                    <tr>
                        <td>{|H&ouml;he (in cm)|}:</td>
                        <td><input type="text" size="10" v-model.number="form.height"></td>
                    </tr>
                    <tr>
                        <td>{|Breite (in cm)|}:</td>
                        <td><input type="text" size="10" v-model.number="form.width"></td>
                    </tr>
                    <tr>
                        <td>{|L&auml;nge (in cm)|}:</td>
                        <td><input type="text" size="10" v-model.number="form.length"></td>
                    </tr>
                    <tr>
                        <td>{|Produkt|}:</td>
                        <td>
                            <select v-model="form.product" required>
                                <option v-for="prod in products" :value="prod.Id" v-if="productAvailable(prod)">{{prod.Name}}</option>
                            </select><i>F&uuml;r Produktwahl Gewicht eingeben!</i>
                        </td>
                    </tr>
                    <tr v-if="serviceAvailable('premium')">
                        <td>{|Premium|}:</td>
                        <td><input type="checkbox" v-model="form.services.premium"></td>
                    </tr>
                </table>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12">
                <h2>{|Sonstiges|}</h2>
                <table>
                    <tbody>
                        <tr>
                            <td>{|Referenzen|}:</td>
                            <td><input type="text" size="36" v-model="form.order_number"></td>
                        </tr>
                        <tr>
                            <td>{|Versicherungssumme|}:</td>
                            <td><input type="text" size="10" v-model="form.total_insured_value"/></td>
                        </tr>
                    </tbody>
                    <tbody v-if="customsRequired()">
                        <tr>
                            <td>{|Rechnungsnummer|}:</td>
                            <td><input type="text" size="36" v-model="form.invoice_number" required="required"></td>
                        </tr>
                        <tr>
                            <td>{|Sendungsart|}:</td>
                            <td>
                                <select v-model="form.shipment_type">
                                    <option v-for="(value, key) in customs_shipment_types" :value="key">{{value}}</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12" v-if="customsRequired()">
                <table>
                    <tr>
                        <th>{|Bezeichnung|}</th>
                        <th>{|Menge|}</th>
                        <th>{|HSCode|}</th>
                        <th>{|Herkunftsland|}</th>
                        <th>{|Einzelwert|}</th>
                        <th>{|Einzelgewicht|}</th>
                        <th>{|Gesamtwert|}</th>
                        <th>{|Gesamtgewicht|}</th>
                        <th><a v-on:click="addPosition"><img src="themes/new/images/add.png"></a></
                        </th>
                    </tr>
                    <tr v-for="(pos, index) in form.positions">
                        <td><input type="text" v-model.trim="pos.bezeichnung" required></td>
                        <td><input type="text" v-model.number="pos.menge" required></td>
                        <td><input type="text" v-model.trim="pos.zolltarifnummer"></td>
                        <td><input type="text" v-model.trim="pos.herkunftsland"></td>
                        <td><input type="text" v-model.number="pos.zolleinzelwert"></td>
                        <td><input type="text" v-model.number="pos.zolleinzelgewicht"></td>
                        <td>{{Number(pos.menge*pos.zolleinzelwert || 0).toFixed(2)}}</td>
                        <td>{{Number(pos.menge*pos.zolleinzelgewicht || 0).toFixed(3)}}</td>
                        <td><a v-on:click="deletePosition(index)"><img src="themes/new/images/delete.svg"></a></td>
                    </tr>
                    <tr>
                        <td colspan="6"></td>
                        <td>{{total_value.toFixed(2)}}</td>
                        <td>{{total_weight.toFixed(3)}}</td>
                    </tr>
                </table>
            </div>
            <div>
                <input class="btnGreen" type="submit" value="{|Paketmarke drucken|}" name="drucken" :disabled="submitting">&nbsp;
                <!--<input type="button" value="{|Andere Versandart auswählen|}" name="anders">&nbsp;-->
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    const createshipmentapp = new Vue({
        el: '#createshipmentapp',
        data: [JSON],
        mounted() {
	    this.autoselectproduct();
        },
        computed: {
            total_value() {
                let sum = 0;
                for (const pos of this.form.positions) {
                    sum += (pos.menge * pos.zolleinzelwert) || 0;
                }
                return sum;
            },
            total_weight() {
                let sum = 0;
                for (const pos of this.form.positions) {
                    sum += (pos.menge * pos.zolleinzelgewicht) || 0;
                }
                return sum;
            }
        },
        methods: {
            submit: function () {
                let app = this;
                app.submitting = true;
                let xhr = new XMLHttpRequest();
                xhr.open('POST', location.href, true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.onload = function () {
                    let json = JSON.parse(this.response);
                    app.messages = json.messages;
                    app.submitting = false;
                }
                xhr.send(JSON.stringify($.extend({submit:'print'}, this.form)));
            },
            addPosition: function () {
                this.form.positions.push({});
            },
            deletePosition: function (index) {
                this.form.positions.splice(index, 1);
            },
            productAvailable: function (product) {
                if (product == undefined)
                    return false;
                if (product.WeightMin > this.form.weight || product.WeightMax < this.form.weight)
                    return false;
                return true;
            },
            serviceAvailable: function (service) {
                if (!this.products.hasOwnProperty(this.form.product))
                    return false;
                return this.products[this.form.product].AvailableServices.indexOf(service) >= 0;
            },
            customsRequired: function () {
                return this.countries[this.form.country].eu == '0';
            },
            autoselectproduct: function () {
	            if (!this.productAvailable(this.products[this.form.product])) {
	                for (prod in this.products) {
	                    if (!this.productAvailable(this.products[prod]))
	                        continue;
	                    this.form.product = prod;
	                    break;
	                }
	            }
            }
        },
        beforeUpdate: function () {
            this.autoselectproduct();
        }
    })
</script>
