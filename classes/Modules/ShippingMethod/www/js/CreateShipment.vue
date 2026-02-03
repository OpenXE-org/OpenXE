<!--
SPDX-FileCopyrightText: 2022-2024 Andreas Palm

SPDX-License-Identifier: LicenseRef-EGPL-3.1
-->

<script setup>
import { useI18n } from 'vue-i18n';
import {computed, onBeforeUpdate, onMounted, ref} from "vue";
import axios from "axios";
import { Button, Checkbox, DatePicker, Dialog, Fieldset, InputNumber, InputText, Select } from "primevue";
import { Column, ColumnGroup, DataTable, Row } from "primevue";
import {AlertErrorHandler} from "@res/js/ajaxErrorHandler";
import HazmatInfo from "./HazmatInfo.vue";

const {t, d, n} = useI18n();

const model = ref(null);
const messages = ref([]);
const products = ref({});
const submitting = ref(false);
const countries = ref({});
const carrier = ref(null);
const dialog = ref(null);
const addressTypes = [
  {name: t('address.type.company'), value: 0},
  {name: t('address.type.parcelStation'), value: 1},
  {name: t('address.type.shop'), value: 2},
  {name: t('address.type.private'), value: 3},
];
const shipmentTypes = [
  {name: t('customs.shipmentType.gift'), value: 0},
  {name: t('customs.shipmentType.documents'), value: 1},
  {name: t('customs.shipmentType.goods'), value: 2},
  {name: t('customs.shipmentType.sample'), value: 3},
  {name: t('customs.shipmentType.return'), value: 4},
];

const totalValue = computed(() => {
  let sum = 0;
  for (const pos of model.value.customsDeclaration.positions) {
    sum += (pos.quantity * pos.itemValue) || 0;
  }
  return sum;
});

const totalWeight = computed(() => {
  let sum = 0;
  for (const pos of model.value.customsDeclaration.positions) {
    sum += (pos.quantity * pos.itemWeight) || 0;
  }
  return sum;
});

const availProducts = computed(() => {
  return Object.values(products.value).filter(productAvailable);
})

function addPosition() {
  model.value.customsDeclaration.positions.push({description: 'EDIT', quantity: 0, hsCode: 'EDIT', itemValue: 0, itemWeight: 0});
}

function deletePosition(index) {
  model.value.customsDeclaration.positions.splice(index, 1);
}

function onCellEditComplete(event) {
  let {data, newValue, field} = event;
  data[field] = newValue;
}

function productAvailable(product) {
  if (product === undefined)
    return false;
  if (product.WeightMin > model.value.package.weight || product.WeightMax < model.value.package.weight)
    return false;
  return true;
}

function serviceAvailable(service) {
  if (!products.value.hasOwnProperty(model.value.productId))
    return false;
  return products.value[model.value.productId].AvailableServices.indexOf(service) >= 0;
}
function customsRequired() {
  return countries.value[model.value.address.country].eu === '0';
}
function autoselectproduct() {
  if (productAvailable(products.value[model.value.productId]))
    return;

  model.value.productId = availProducts.value[0]?.Id;
}

function openDialog(name) {
  dialog.value = name;
}

function submit() {
  submitting.value = true;
  axios.post(location.href, { ...model.value, submit: 'print'})
      .then(response => messages.value = response.data.messages)
      .catch(AlertErrorHandler)
      .finally(() => submitting.value = false);
}

onMounted(() => {
  const data = window.createShipmentData;
  if (data === undefined)
    return;
  if (data.model.services.pickupDate)
    data.model.services.pickupDate = new Date(data.model.services.pickupDate);
  if (data.model.services.pickupTimeFrom)
    data.model.services.pickupTimeFrom = new Date(data.model.services.pickupTimeFrom);
  if (data.model.services.pickupTimeTill)
    data.model.services.pickupTimeTill = new Date(data.model.services.pickupTimeTill);

  products.value = data.products;
  countries.value = data.countries;
  model.value = data.model;
  carrier.value = data.carrier;
  autoselectproduct();
});

onBeforeUpdate(autoselectproduct);
</script>

<template>
  <div class="gap-1" v-if="model">
    <div v-for="msg in messages" :class="msg.class">{{msg.text}}</div>
    <div>
      <h1>{{ t('title', { carrier: carrier }) }}</h1>
    </div>
    <div class="flex flex-row gap-x-4">
      <Fieldset class="basis-1/3" :legend="t('address.recipient')">
        <div class="grid grid-cols-classic-form gap-1">
          <div>{{ t('address.addressType')}}:</div>
          <Select v-model="model.address.addresstype" :options="addressTypes" option-label="name" option-value="value" />
          <template v-if="model.address.addresstype === 0">
            <div>{{ t('address.companyName') }}:</div>
            <InputText v-model.trim="model.address.companyName" />
            <div>{{ t('address.companyDivision') }}:</div>
            <InputText v-model.trim="model.address.companyDivision" />
          </template>
          <template v-else>
            <div>{{ t('address.name') }}:</div>
            <InputText v-model.trim="model.address.name" />
          </template>
          <template v-if="model.address.addresstype === 0 || model.address.addresstype === 3">
            <div>{{ t('address.contactName') }}:</div>
            <InputText v-model.trim="model.address.contactName" />
          </template>
          <template v-if="model.address.addresstype === 1 || model.address.addresstype === 2">
            <div>{{ t('address.postNumber') }}:</div>
            <InputText v-model.trim="model.address.postnumber" />
          </template>
          <template v-if="model.address.addresstype === 0 || model.address.addresstype === 3">
            <div>{{ t('address.streetAndNo') }}:</div>
            <div class="flex flex-row">
              <InputText v-model.trim="model.address.street" class="flex-auto" />
              <InputText v-model.trim="model.address.streetnumber" class="flex-none" style="width: 5rem" />
            </div>
          </template>
          <template v-if="model.address.addresstype === 1">
            <div>{{ t('address.parcelStationNumber') }}:</div>
            <InputText type="text" size="10" v-model.trim="model.address.parcelstationNumber" />
          </template>
          <template v-if="model.address.addresstype === 2">
            <div>{{ t('address.shopNumber') }}:</div>
            <InputText v-model.trim="model.address.postofficeNumber" />
          </template>
          <template v-if="model.address.addresstype === 0 || model.address.addresstype === 3">
            <div>{{ t('address.addressLine2') }}:</div>
            <InputText v-model.trim="model.address.address2" />
          </template>
          <div>{{ t('address.zip_city') }}:</div>
          <div class="flex flex-row">
            <InputText v-model.trim="model.address.zip" class="flex-none" style="width: 6rem" />
            <InputText v-model.trim="model.address.city" class="flex-auto" />
          </div>
          <div>{{ t('address.state') }}:</div>
          <InputText v-model.trim="model.address.state" />
          <div>{{ t('address.country') }}:</div>
          <Select v-model="model.address.country" :options="Object.values(countries)" option-label="name" option-value="iso" />
          <div>{{ t('address.email') }}:</div>
          <InputText v-model.trim="model.address.email" />
          <div>{{ t('address.phone') }}:</div>
          <InputText v-model.trim="model.address.phone" />
        </div>
      </Fieldset>
      <Fieldset class="basis-1/3" v-once :legend="t('address.shippingAddress')">
        <div class="grid grid-cols-classic-form gap-1">
          <div>{{ t('address.name') }}</div>
          <div>{{model.address.original.name}}</div>
          <div>{{ t('address.contactName') }}</div>
          <div>{{model.address.original.ansprechpartner}}</div>
          <div>{{ t('address.companyDivision') }}</div>
          <div>{{model.address.original.abteilung}}</div>
          <div>{{ t('address.companySubdivision') }}</div>
          <div>{{model.address.original.unterabteilung}}</div>
          <div>{{ t('address.additionalInfo') }}</div>
          <div>{{model.address.original.adresszusatz}}</div>
          <div>{{ t('address.street') }}</div>
          <div>{{model.address.original.strasse}}</div>
          <div>{{ t('address.zip_city') }}</div>
          <div>{{model.address.original.plz}} {{model.address.original.ort}}</div>
          <div>{{ t('address.state') }}</div>
          <div>{{model.address.original.bundesland}}</div>
          <div>{{ t('address.country') }}</div>
          <div>{{model.address.original.land}}</div>
        </div>
      </Fieldset>
      <Fieldset class="basis-1/3" :legend="t('package')">
        <div class="grid grid-cols-2 gap-1">
          <label>{{ t('packageWeight') }}:</label>
          <InputNumber v-model="model.package.weight" :min-fraction-digits="1" :max-fraction-digits="2" :step="0.1" :min="0" suffix=" kg" show-buttons @value-change="autoselectproduct" />
          <label>{{ t('packageHeight') }}:</label>
          <InputNumber v-model="model.package.height" suffix=" cm" show-buttons />
          <label>{{ t('packageWidth') }}:</label>
          <InputNumber v-model="model.package.width" suffix=" cm" show-buttons />
          <label>{{ t('packageLength') }}:</label>
          <InputNumber v-model="model.package.length" suffix=" cm" show-buttons />
          <label>{{ t('shippingProduct') }}:</label>
          <div>
            <Select v-model="model.productId" required :options="availProducts" option-label="Name" option-value="Id" />
            <i v-if="availProducts.length == 0 && model.package.weight == 0">Für Produktwahl Gewicht eingeben!</i>
          </div>
          <template v-if="serviceAvailable('premium')">
            <label>{{ t('services.premium') }}:</label>
            <Checkbox v-model="model.services.premium" binary />
          </template>
          <template v-if="serviceAvailable('pickup')">
            <label>{{ t('services.pickup') }}:</label>
            <Checkbox v-model="model.services.pickup" binary />
          </template>
          <template v-if="model.services.pickup && serviceAvailable('pickup_date')">
            <label>{{ t('services.pickupDate') }}:</label>
            <DatePicker v-model="model.services.pickupDate" />
          </template>
          <template v-if="model.services.pickup && serviceAvailable('pickup_time')">
            <label>{{ t('services.pickupTime') }}:</label>
            <div class="flex flex-row">
              <DatePicker v-model="model.services.pickupTimeFrom" time-only hour-format="24" />
              <DatePicker v-model="model.services.pickupTimeTill" time-only hour-format="24" />
            </div>
          </template>
          <template v-if="serviceAvailable('hazmat')">
            <label>{{ t('services.hazmat') }}:</label>
            <Button icon="pi pi-cog" variant="text" @click="openDialog('hazmat')" rounded size="large" />
          </template>
        </div>
      </Fieldset>
    </div>
    <div class="flex flex-row gap-x-4">
      <Fieldset class="basis-1/3" :legend="t('other')">
        <div class="grid grid-cols-classic-form gap-1">
          <label>{{ t('references') }}:</label>
          <InputText v-model="model.reference" />
          <label>{{ t('insuredValue') }}:</label>
          <InputNumber v-model="model.insuranceValue" mode="currency" currency="EUR" :min="0" show-buttons />
          <template v-if="customsRequired()">
            <label>{{ t('document.invoiceNumber') }}:</label>
            <InputText v-model="model.customsDeclaration.invoiceNumber" required="required" />
            <label>{{ t('shipmentType') }}:</label>
            <Select v-model="model.customsDeclaration.shipmentType" :options="shipmentTypes" option-label="name" option-value="value" />
          </template>
          <template v-else>
            <label>{{ t('content')}}:</label>
            <InputText v-model="model.content" />
          </template>
        </div>
      </Fieldset>
      <Fieldset v-if="customsRequired()" :legend="t('customs.declaration')" class="basis-2/3">
        <DataTable :value="model.customsDeclaration.positions" edit-mode="cell" @cell-edit-complete="onCellEditComplete">
          <Column field="description" :header="t('common.description')">
            <template #editor="{data,field}">
              <InputText v-model="data[field]" />
            </template>
          </Column>
          <Column field="quantity" :header="t('common.amount')">
            <template #editor="{data,field}">
              <InputNumber v-model="data[field]" :min="0" :max-fraction-digits="0" />
            </template>
          </Column>
          <Column field="hsCode" :header="t('customs.hscode')">
            <template #editor="{data,field}">
              <InputText v-model="data[field]" />
            </template>
          </Column>
          <Column field="originCountryCode" :header="t('product.originCountry')">
            <template #editor="{data,field}">
              <Select v-model="data[field]" :options="Object.values(countries)" option-label="name" option-value="iso" />
            </template>
          </Column>
          <Column field="itemValue" :header="t('customs.itemValue')">
            <template #body="{data,field}">{{ n(data[field], 'currency')}}</template>
            <template #editor="{data,field}">
              <InputNumber v-model="data[field]" :min="0" mode="currency" currency="EUR" />
            </template>
          </Column>
          <Column field="itemWeight" :header="t('customs.itemWeight')">
            <template #body="{data,field}">{{ n(data[field], 'weight')}}</template>
            <template #editor="{data,field}">
              <InputNumber v-model="data[field]" :min="0" suffix=" kg" :min-fraction-digits="3" :max-fraction-digits="3" />
            </template>
          </Column>
          <Column field="totalValue" :header="t('common.totalValue')">
            <template #body="{data}">
              {{ n(Number(data.quantity*data.itemValue || 0), 'currency') }}
            </template>
          </Column>
          <Column field="totalWeight" :header="t('common.totalWeight')">
            <template #body="{data}">
              {{ n(Number(data.quantity*data.itemWeight || 0), 'weight') }}
            </template>
          </Column>
          <Column>
            <template #body="{index}">
              <Button @click="deletePosition(index)" icon="pi pi-trash" rounded />
            </template>
          </Column>
          <ColumnGroup type="footer">
            <Row>
              <Column>
                <template #footer>
                  <Button @click="addPosition" icon="pi pi-plus" rounded />
                </template>
              </Column>
              <Column colspan="5" />
              <Column>
                <template #footer>{{ n(totalValue, 'currency') }}</template>
              </Column>
              <Column>
                <template #footer>{{ n(totalWeight, 'weight') }}</template>
              </Column>
            </Row>
          </ColumnGroup>
        </DataTable>
      </Fieldset>
    </div>
    <div>
      <Button :label="t('printLabel')" :disabled="submitting" @click="submit" class="my-4" />
    </div>
    <Dialog :visible="dialog == 'hazmat'" @update:visible="dialog = null" modal style="width: 40vw" :header="t('hazmat.info')">
      <HazmatInfo v-model="model.services.hazmat" />
    </Dialog>
  </div>
</template>

<i18n src="./locales.yaml" lang="yaml"></i18n>